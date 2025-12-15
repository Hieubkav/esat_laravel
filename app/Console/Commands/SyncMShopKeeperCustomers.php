<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MShopKeeperCustomer;
use App\Services\MShopKeeperService;
use Carbon\Carbon;

class SyncMShopKeeperCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mshopkeeper:sync-customers
                            {--force : Force sync even if recently synced}
                            {--dry-run : Show what would be synced without actually syncing}
                            {--clear : Clear all existing data before sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync customers from MShopKeeper API to database';

    protected MShopKeeperService $mshopkeeperService;

    public function __construct(MShopKeeperService $mshopkeeperService)
    {
        parent::__construct();
        $this->mshopkeeperService = $mshopkeeperService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting MShopKeeper Customers Sync...');

        $startTime = microtime(true);
        $stats = [
            'total_api' => 0,
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'skipped' => 0,
        ];

        try {
            // Clear existing data if requested
            if ($this->option('clear')) {
                $this->handleClearData();
            }

            // Sync customers
            $this->info('👥 Syncing customers...');
            $customerStats = $this->syncCustomers();
            foreach ($customerStats as $key => $value) {
                $stats[$key] += $value;
            }

            // Show final statistics
            $this->showFinalStats($stats, $startTime);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Sync failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    /**
     * Clear existing data
     */
    private function handleClearData(): void
    {
        if (!$this->confirm('⚠️  This will delete ALL existing customer data. Are you sure?')) {
            $this->info('Sync cancelled.');
            exit(0);
        }

        $count = MShopKeeperCustomer::count();
        MShopKeeperCustomer::truncate();
        $this->info("🗑️  Cleared {$count} existing customers");
    }

    /**
     * Sync customers from API với phân trang
     */
    private function syncCustomers(): array
    {
        $stats = ['total_api' => 0, 'created' => 0, 'updated' => 0, 'errors' => 0, 'skipped' => 0];
        $allCustomers = [];
        $page = 1;
        $limit = 100; // Max limit theo API doc
        $totalFromAPI = 0;

        $this->info('📥 Fetching customers from API...');

        // Lấy tất cả customers qua phân trang
        $maxPages = 100; // Safety limit để tránh infinite loop

        do {
            $this->info("   → Fetching page {$page}...");

            $result = $this->mshopkeeperService->getCustomers($page, $limit);

            if (!$result['success']) {
                throw new \Exception('Failed to fetch customers from API: ' . ($result['error']['message'] ?? 'Unknown error'));
            }

            $pageCustomers = $result['data']['customers'] ?? [];
            $totalFromAPI = $result['data']['total_customers'] ?? $result['data']['total'] ?? 0;

            $allCustomers = array_merge($allCustomers, $pageCustomers);

            $this->info("   ✓ Page {$page}: " . count($pageCustomers) . " customers (Total: {$totalFromAPI})");

            $page++;

            // Dừng khi:
            // 1. Không còn dữ liệu trong page hiện tại
            // 2. Đã lấy hết customers theo total
            // 3. Đạt max pages (safety)
        } while (
            count($pageCustomers) > 0 &&
            count($allCustomers) < $totalFromAPI &&
            $page <= $maxPages
        );

        $stats['total_api'] = count($allCustomers);

        $this->info("📊 Total customers from API: {$stats['total_api']} / {$totalFromAPI}");

        if ($this->option('dry-run')) {
            $this->info('🔍 [DRY RUN] Would process customers...');
            return $stats;
        }

        // Process each customer
        $progressBar = $this->output->createProgressBar($stats['total_api']);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        foreach ($allCustomers as $index => $apiCustomer) {
            try {
                $result = $this->processCustomer($apiCustomer);
                $stats[$result]++;

                // Update progress bar message
                $progressBar->setMessage("Processing: " . ($apiCustomer['Name'] ?? 'Unknown'));

            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("Error processing customer {$index}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return $stats;
    }

    /**
     * Process single customer
     */
    private function processCustomer(array $apiCustomer): string
    {
        $mshopkeeperId = $apiCustomer['Id'] ?? $apiCustomer['CustomerID'] ?? null;

        if (!$mshopkeeperId) {
            throw new \Exception('Customer ID not found in API data');
        }

        $normalizedData = MShopKeeperCustomer::normalizeApiData($apiCustomer);

        $customer = MShopKeeperCustomer::where('mshopkeeper_id', $mshopkeeperId)->first();

        if ($customer) {
            // Update existing - BẢO TỒN PASSWORD LOCAL
            $existingPassword = $customer->password;
            $existingPlainPassword = $customer->plain_password;

            $customer->update($normalizedData);

            // Khôi phục password nếu có
            if ($existingPassword) {
                $customer->update([
                    'password' => $existingPassword,
                    'plain_password' => $existingPlainPassword,
                ]);
            }

            $customer->markAsSynced($apiCustomer);
            return 'updated';
        } else {
            // Create new
            $customer = MShopKeeperCustomer::create($normalizedData);
            $customer->markAsSynced($apiCustomer);
            return 'created';
        }
    }

    /**
     * Show final statistics
     */
    private function showFinalStats(array $stats, float $startTime): void
    {
        $duration = round(microtime(true) - $startTime, 2);

        $this->newLine();
        $this->info('✅ Sync completed successfully!');
        $this->info("⏱️  Duration: {$duration} seconds");

        // Show sync stats
        $this->table(
            ['Action', 'Count'],
            [
                ['API Customers', $stats['total_api']],
                ['Created', $stats['created']],
                ['Updated', $stats['updated']],
                ['Errors', $stats['errors']],
                ['Skipped', $stats['skipped']],
            ]
        );

        // Show database stats
        $syncStats = MShopKeeperCustomer::getSyncStats();
        $this->newLine();
        $this->info('📊 Database Statistics:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Customers', $syncStats['total']],
                ['Successfully Synced', $syncStats['synced']],
                ['Sync Errors', $syncStats['errors']],
                ['Pending Sync', $syncStats['pending']],
                ['Last Sync', $syncStats['last_sync'] ? Carbon::parse($syncStats['last_sync'])->format('Y-m-d H:i:s') : 'Never'],
            ]
        );

        if ($stats['errors'] > 0) {
            $this->warn("⚠️  {$stats['errors']} errors occurred. Check logs for details.");
        }
    }
}
