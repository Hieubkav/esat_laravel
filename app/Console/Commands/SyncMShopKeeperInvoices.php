<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MShopKeeperService;
use App\Models\MShopKeeperInvoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncMShopKeeperInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mshopkeeper:sync-invoices
                            {--force : Force sync even if recently synced}
                            {--dry-run : Show what would be synced without actually syncing}
                            {--clear : Clear all existing data before sync}
                            {--from-date= : Sync from specific date (Y-m-d format)}
                            {--to-date= : Sync to specific date (Y-m-d format)}
                            {--customer-id= : Sync for specific customer ID}
                            {--branch-id= : Sync for specific branch ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync invoices from MShopKeeper API to database';

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
        $this->info('🚀 Starting MShopKeeper Invoices Sync...');

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

            // Determine date range
            $dateRange = $this->determineDateRange();
            $this->info("📅 Syncing invoices from {$dateRange['from']} to {$dateRange['to']}");

            // Sync invoices from API
            $this->info('📥 Fetching invoices from API...');
            $syncStats = $this->syncInvoices($dateRange, $startTime);
            foreach ($syncStats as $key => $value) {
                $stats[$key] += $value;
            }

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            $this->displayResults($stats, $duration);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Sync failed: ' . $e->getMessage());
            Log::error('MShopKeeper Invoices Sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Clear existing data
     */
    private function handleClearData(): void
    {
        if (!$this->option('dry-run')) {
            $count = MShopKeeperInvoice::count();
            if ($count > 0) {
                if ($this->confirm("⚠️  This will delete {$count} existing invoices. Continue?")) {
                    MShopKeeperInvoice::truncate();
                    $this->info("🗑️  Cleared {$count} existing invoices");
                } else {
                    $this->info('❌ Sync cancelled');
                    exit(Command::FAILURE);
                }
            }
        } else {
            $count = MShopKeeperInvoice::count();
            $this->info("🔍 [DRY RUN] Would clear {$count} existing invoices");
        }
    }

    /**
     * Determine date range for sync
     */
    private function determineDateRange(): array
    {
        $fromDate = $this->option('from-date');
        $toDate = $this->option('to-date');

        // Default: sync last 7 days if no dates specified
        if (!$fromDate && !$toDate) {
            $fromDate = Carbon::now()->subDays(7)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
        } elseif (!$fromDate) {
            $fromDate = Carbon::parse($toDate)->subDays(7)->format('Y-m-d');
        } elseif (!$toDate) {
            $toDate = Carbon::parse($fromDate)->addDays(7)->format('Y-m-d');
        }

        return [
            'from' => $fromDate,
            'to' => $toDate . 'T23:59:59.999Z',
        ];
    }

    /**
     * Sync invoices from API với phân trang
     */
    private function syncInvoices(array $dateRange, float $startTime): array
    {
        $stats = [
            'total_api' => 0,
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'skipped' => 0,
        ];

        $page = 1;
        $limit = 100;
        $totalFromAPI = 0;

        do {
            // Build request parameters
            $requestParams = [
                'Page' => $page,
                'Limit' => $limit,
                'SortField' => 'InvoiceNumber',
                'SortType' => 1,
                'FromDate' => $dateRange['from'],
                'ToDate' => $dateRange['to'],
                'CustomerID' => $this->option('customer-id'),
                'BranchID' => $this->option('branch-id'),
                'DateRangeType' => 1, // Theo ngày hóa đơn
            ];

            // Remove null values
            $requestParams = array_filter($requestParams, fn($value) => $value !== null);

            $this->info("📄 Fetching page {$page} (limit: {$limit})...");

            // Call API
            $result = $this->mshopkeeperService->getInvoicesPaging($requestParams);

            if (!$result['success']) {
                throw new \Exception('API call failed: ' . $result['error']['message']);
            }

            $invoices = $result['data']['Data'] ?? [];
            $totalFromAPI = $result['data']['Total'] ?? 0;
            $stats['total_api'] = $totalFromAPI;

            if (empty($invoices)) {
                $this->info("📭 No invoices found on page {$page}");
                break;
            }

            $this->info("📦 Processing " . count($invoices) . " invoices from page {$page}...");

            // Process each invoice
            foreach ($invoices as $invoiceData) {
                try {
                    if ($this->option('dry-run')) {
                        $this->line("🔍 [DRY RUN] Would process invoice: {$invoiceData['InvoiceNumber']}");
                        $stats['skipped']++;
                        continue;
                    }

                    $result = $this->processInvoice($invoiceData);
                    $stats[$result]++;

                } catch (\Exception $e) {
                    $stats['errors']++;
                    $invoiceNumber = $invoiceData['InvoiceNumber'] ?? 'unknown';
                    $this->error("❌ Error processing invoice {$invoiceNumber}: " . $e->getMessage());
                    Log::error('Invoice processing error', [
                        'invoice' => $invoiceData,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $page++;

            // Show progress
            $processed = ($page - 1) * $limit;
            $this->info("✅ Processed {$processed}/{$totalFromAPI} invoices");

        } while (count($invoices) === $limit && $processed < $totalFromAPI);

        return $stats;
    }

    /**
     * Process single invoice
     */
    private function processInvoice(array $invoiceData): string
    {
        $invoiceId = $invoiceData['InvoiceId'];

        // Check if invoice already exists
        $existingInvoice = MShopKeeperInvoice::where('mshopkeeper_invoice_id', $invoiceId)->first();

        $mappedData = $this->mapInvoiceData($invoiceData);

        if ($existingInvoice) {
            // Update existing invoice
            $existingInvoice->update($mappedData);
            $existingInvoice->markAsSynced();
            return 'updated';
        } else {
            // Create new invoice
            $invoice = MShopKeeperInvoice::create($mappedData);
            $invoice->markAsSynced();
            return 'created';
        }
    }

    /**
     * Map API data to database fields
     */
    private function mapInvoiceData(array $data): array
    {
        return [
            'mshopkeeper_invoice_id' => $data['InvoiceId'],
            'invoice_number' => $data['InvoiceNumber'],
            'invoice_type' => $data['InvoiceType'] ?? null,
            'invoice_date' => isset($data['InvoiceDate']) ? Carbon::parse($data['InvoiceDate']) : null,
            'invoice_time' => isset($data['InvoiceTime']) ? Carbon::parse($data['InvoiceTime']) : null,
            'branch_id' => $data['BranchId'] ?? null,
            'branch_name' => $data['BranchName'] ?? null,
            'total_amount' => $data['TotalAmount'] ?? 0,
            'cost_amount' => $data['CostAmount'] ?? 0,
            'tax_amount' => $data['TaxAmount'] ?? 0,
            'total_item_amount' => $data['TotalItemAmount'] ?? 0,
            'vat_amount' => $data['VATAmount'] ?? 0,
            'discount_amount' => $data['DiscountAmount'] ?? 0,
            'cash_amount' => $data['CashAmount'] ?? 0,
            'card_amount' => $data['CardAmount'] ?? 0,
            'voucher_amount' => $data['VoucherAmount'] ?? 0,
            'debit_amount' => $data['DebitAmount'] ?? 0,
            'actual_amount' => $data['ActualAmount'] ?? 0,
            'customer_name' => $data['CustomerName'] ?? null,
            'customer_tel' => $data['Tel'] ?? null,
            'customer_address' => $data['Address'] ?? null,
            'member_level_name' => $data['MemberLevelName'] ?? null,
            'cashier' => $data['Cashier'] ?? null,
            'sale_staff' => $data['SaleStaff'] ?? null,
            'payment_status' => $data['PaymentStatus'] ?? null,
            'is_cod' => $data['IsCOD'] ?? false,
            'addition_bill_type' => $data['AdditionBillType'] ?? null,
            'sale_channel_name' => $data['SaleChannelName'] ?? null,
            'return_exchange_ref_no' => $data['ReturnExchangeRefNo'] ?? null,
            'has_connected_shipping_partner' => $data['HasConnectedShippingPartner'] ?? false,
            'delivery_code' => $data['DeliveryCode'] ?? null,
            'shipping_partner_name' => $data['ShippingPartnerName'] ?? null,
            'partner_status' => $data['PartnerStatus'] ?? null,
            'delivery_date' => isset($data['DeliveryDate']) ? Carbon::parse($data['DeliveryDate']) : null,
            'point' => $data['Point'] ?? 0,
            'barcode' => $data['Barcode'] ?? null,
            'note' => $data['Note'] ?? null,
            'raw_data' => $data,
        ];
    }

    /**
     * Display sync results
     */
    private function displayResults(array $stats, float $duration): void
    {
        $this->newLine();
        $this->info('🎉 Sync completed successfully!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total from API', $stats['total_api']],
                ['Created', $stats['created']],
                ['Updated', $stats['updated']],
                ['Errors', $stats['errors']],
                ['Skipped', $stats['skipped']],
                ['Duration', $duration . 's'],
            ]
        );

        // Log success
        Log::info('MShopKeeper Invoices Sync completed', $stats);
    }
}
