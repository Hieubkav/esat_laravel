<?php

namespace App\Console\Commands;

use App\Models\MShopKeeperCustomerPoint;
use App\Services\MShopKeeperService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncMShopKeeperCustomerPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mshopkeeper:sync-customer-points
                            {--force : Force sync even if recently synced}
                            {--dry-run : Show what would be synced without actually syncing}
                            {--clear : Clear all existing data before sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync customer points from MShopKeeper API to database';

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
        $this->info('🚀 Starting MShopKeeper Customer Points Sync...');

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

            // Sync customer points
            $this->info('💎 Syncing customer points...');
            $pointStats = $this->syncCustomerPoints();
            foreach ($pointStats as $key => $value) {
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
        if (!$this->confirm('⚠️  This will delete ALL existing customer points data. Are you sure?')) {
            $this->info('Sync cancelled.');
            exit(0);
        }

        $count = MShopKeeperCustomerPoint::count();
        MShopKeeperCustomerPoint::truncate();
        $this->info("🗑️  Cleared {$count} existing customer points");
    }

    /**
     * Sync customer points from API với phân trang
     */
    private function syncCustomerPoints(): array
    {
        $stats = ['total_api' => 0, 'created' => 0, 'updated' => 0, 'errors' => 0, 'skipped' => 0];
        $allCustomerPoints = [];
        $page = 1;
        $limit = 100; // Max limit theo API doc
        $totalFromAPI = 0;

        $this->info('📥 Fetching customer points from API...');

        // Lấy tất cả customer points qua phân trang
        $maxPages = 100; // Safety limit để tránh infinite loop

        do {
            $this->info("   → Fetching page {$page}...");

            $result = $this->mshopkeeperService->getCustomersPointPaging($page, $limit);

            if (!$result['success']) {
                throw new \Exception('Failed to fetch customer points from API: ' . ($result['error']['message'] ?? 'Unknown error'));
            }

            $customerPoints = $result['data']['customer_points'] ?? [];
            $totalFromAPI = $result['data']['total_customer_points'] ?? 0;

            if (empty($customerPoints)) {
                $this->info("   → No customer points found on page {$page}");
                break;
            }

            $allCustomerPoints = array_merge($allCustomerPoints, $customerPoints);
            $this->info("   → Found " . count($customerPoints) . " customer points on page {$page}");

            $page++;

            // Safety check
            if ($page > $maxPages) {
                $this->warn("⚠️  Reached maximum pages limit ({$maxPages}). Stopping to prevent infinite loop.");
                break;
            }

        } while (count($customerPoints) === $limit && count($allCustomerPoints) < $totalFromAPI);

        $stats['total_api'] = count($allCustomerPoints);

        $this->info("📊 Total customer points from API: {$stats['total_api']}");
        $this->info("📊 Total from API response: {$totalFromAPI}");

        if ($this->option('dry-run')) {
            $this->info('🔍 DRY RUN - No data will be saved');
            return $stats;
        }

        // Process each customer point
        $progressBar = $this->output->createProgressBar($stats['total_api']);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        foreach ($allCustomerPoints as $index => $apiCustomerPoint) {
            try {
                $result = $this->processCustomerPoint($apiCustomerPoint);
                $stats[$result]++;

                // Update progress bar message
                $progressBar->setMessage("Processing: " . ($apiCustomerPoint['FullName'] ?? 'Unknown'));

            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("Error processing customer point {$index}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return $stats;
    }

    /**
     * Process single customer point
     */
    private function processCustomerPoint(array $apiCustomerPoint): string
    {
        $originalId = $apiCustomerPoint['OriginalId'] ?? null;

        if (!$originalId) {
            throw new \Exception('OriginalId not found in API data');
        }

        $normalizedData = MShopKeeperCustomerPoint::normalizeApiData($apiCustomerPoint);

        $customerPoint = MShopKeeperCustomerPoint::where('original_id', $originalId)->first();

        if ($customerPoint) {
            // Update existing
            $customerPoint->update($normalizedData);
            $customerPoint->markAsSynced($apiCustomerPoint);
            return 'updated';
        } else {
            // Create new
            $customerPoint = MShopKeeperCustomerPoint::create($normalizedData);
            $customerPoint->markAsSynced($apiCustomerPoint);
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
        $this->newLine();

        // Create stats table
        $headers = ['Metric', 'Count'];
        $rows = [
            ['Total from API', $stats['total_api']],
            ['Created', $stats['created']],
            ['Updated', $stats['updated']],
            ['Errors', $stats['errors']],
            ['Skipped', $stats['skipped']],
            ['Duration', $duration . 's'],
        ];

        $this->table($headers, $rows);

        // Log final stats
        Log::info('MShopKeeper Customer Points Sync completed', [
            'stats' => $stats,
            'duration' => $duration
        ]);
    }
}
