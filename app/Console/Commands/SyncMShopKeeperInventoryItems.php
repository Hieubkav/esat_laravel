<?php

namespace App\Console\Commands;

use App\Models\MShopKeeperInventoryItem;
use App\Models\MShopKeeperInventoryStock;
use App\Services\MShopKeeperService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SyncMShopKeeperInventoryItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mshopkeeper:sync-inventory-items
                            {--force : Force sync even if recently synced}
                            {--dry-run : Show what would be synced without actually syncing}
                            {--clear : Clear all existing data before sync}
                            {--include-inventory : Include inventory stock information}
                            {--sync-by-category : Sync products by category to get category information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync inventory items from MShopKeeper API to database';

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
        // Tăng execution time cho sync lớn
        set_time_limit(600); // 10 phút
        ini_set('memory_limit', '512M'); // Tăng memory limit

        $this->info('🚀 Starting MShopKeeper Inventory Items Sync...');
        $this->info('⏱️ PHP execution time limit: ' . ini_get('max_execution_time') . 's');
        $this->info('💾 Memory limit: ' . ini_get('memory_limit'));

        $startTime = microtime(true);
        $stats = [
            'total_api' => 0,
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'skipped' => 0,
            'stocks_created' => 0,
            'stocks_updated' => 0,
        ];

        try {
            // Clear existing data if requested
            if ($this->option('clear')) {
                $this->handleClearData();
            }

            // Sync inventory items
            $this->info('📦 Syncing inventory items...');
            $inventoryStats = $this->syncInventoryItems($startTime);
            foreach ($inventoryStats as $key => $value) {
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
        if (!$this->confirm('⚠️  This will delete ALL existing inventory data. Are you sure?')) {
            $this->info('Sync cancelled.');
            exit(0);
        }

        $itemCount = MShopKeeperInventoryItem::count();
        $stockCount = MShopKeeperInventoryStock::count();
        
        DB::transaction(function () {
            MShopKeeperInventoryStock::truncate();
            MShopKeeperInventoryItem::truncate();
        });
        
        $this->info("🗑️  Cleared {$itemCount} inventory items and {$stockCount} stock records");
    }

    /**
     * Sync inventory items from API với phân trang
     */
    private function syncInventoryItems(float $startTime): array
    {
        $stats = [
            'total_api' => 0, 
            'created' => 0, 
            'updated' => 0, 
            'errors' => 0, 
            'skipped' => 0,
            'stocks_created' => 0,
            'stocks_updated' => 0
        ];
        
        $allInventoryItems = [];
        $page = 1;
        $limit = 100; // Increased limit for better performance
        $totalFromAPI = 0;

        $this->info('📥 Fetching inventory items from API...');

        // Sync sản phẩm với thông tin category từ API
        if ($this->option('sync-by-category')) {
            $this->info('✅ API trả về ItemCategoryId - sync bình thường.');
            // Không cần logic đặc biệt, API đã có category info
        }

        // Parameters for API call (sync tất cả)
        $apiParams = [
            'Page' => $page,
            'Limit' => $limit,
            'SortField' => 'Name',
            'SortType' => 1,
            'IncludeInventory' => $this->option('include-inventory'),
            'IncludeInActive' => false
        ];

        // Lấy tất cả inventory items qua phân trang
        $maxPages = 100; // Safety limit để tránh infinite loop

        do {
            $this->info("   → Fetching page {$page}...");

            $apiParams['Page'] = $page;
            $result = $this->mshopkeeperService->getInventoryItemsPagingWithDetail($apiParams);

            if (!$result['success']) {
                throw new \Exception('Failed to fetch inventory items from API: ' . ($result['error']['message'] ?? 'Unknown error'));
            }

            $inventoryItems = $result['data']['inventory_items'] ?? [];
            $totalFromAPI = $result['data']['total_inventory_items'] ?? 0;

            if (empty($inventoryItems)) {
                $this->info("   → No inventory items found on page {$page}");
                break;
            }

            $allInventoryItems = array_merge($allInventoryItems, $inventoryItems);
            $this->info("   → Found " . count($inventoryItems) . " inventory items on page {$page}");

            $page++;

            // Safety check
            if ($page > $maxPages) {
                $this->warn("⚠️  Reached maximum pages limit ({$maxPages}). Stopping to prevent infinite loop.");
                break;
            }

        } while (count($inventoryItems) === $limit && count($allInventoryItems) < $totalFromAPI);

        $stats['total_api'] = count($allInventoryItems);

        $this->info("📊 Total inventory items from API: {$stats['total_api']}");
        $this->info("📊 Total from API response: {$totalFromAPI}");

        if ($this->option('dry-run')) {
            $this->info('🔍 DRY RUN - No data will be saved');
            return $stats;
        }

        // Process each inventory item
        $progressBar = $this->output->createProgressBar($stats['total_api']);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        $batchSize = 20; // Process in batches
        $batches = array_chunk($allInventoryItems, $batchSize);

        foreach ($batches as $batchIndex => $batch) {
            // Check execution time every batch
            $currentTime = microtime(true);
            $elapsedTime = $currentTime - $startTime;

            if ($elapsedTime > 540) { // 9 phút (để lại 1 phút buffer)
                $processedItems = $batchIndex * $batchSize;
                $this->warn("⏰ Approaching time limit. Processed {$processedItems} items. Stopping to prevent timeout.");
                break;
            }

            // Reset time limit nếu cần
            set_time_limit(600);

            // Process batch
            foreach ($batch as $index => $apiInventoryItem) {
                try {
                    $result = $this->processInventoryItem($apiInventoryItem);
                    $stats[$result['item_action']]++;

                    // Process stocks if available
                    if (isset($result['stock_stats'])) {
                        $stats['stocks_created'] += $result['stock_stats']['created'];
                        $stats['stocks_updated'] += $result['stock_stats']['updated'];
                    }

                    // Update progress bar message
                    $progressBar->setMessage("Processing: " . ($apiInventoryItem['Name'] ?? 'Unknown'));

                } catch (\Exception $e) {
                    $stats['errors']++;
                    $this->error("Error processing inventory item: " . $e->getMessage());
                }
                $progressBar->advance();
            }

            // Memory cleanup every 5 batches
            if ($batchIndex % 5 === 0) {
                gc_collect_cycles();
            }
        }

        $progressBar->finish();
        $this->newLine();

        return $stats;
    }

    /**
     * Sync inventory items với category mapping (hiệu quả hơn)
     */
    private function syncInventoryItemsWithCategoryMapping(float $startTime): array
    {
        $this->info("🚀 Sync tất cả sản phẩm một lần và map category sau...");

        // Sync tất cả sản phẩm bình thường trước
        $stats = $this->syncAllInventoryItems($startTime);

        $this->info("📂 Bắt đầu map category cho sản phẩm...");

        // Map category cho sản phẩm dựa trên logic business hoặc tên sản phẩm
        $this->mapProductCategories();

        return $stats;
    }

    /**
     * Map category cho sản phẩm dựa trên logic business
     */
    private function mapProductCategories(): void
    {
        // Lấy danh sách danh mục
        $categories = \App\Models\MShopKeeperCategory::where('inactive', false)->get();
        $categoryMap = $categories->keyBy('name');

        $this->info("📋 Found {$categories->count()} categories for mapping");

        // Map sản phẩm theo tên (logic đơn giản)
        $mappingRules = [
            'bánh' => ['Bánh ngọt', 'Bánh kem'],
            'nước' => ['Nước uống', 'Đồ uống'],
            'cà phê' => ['Cà phê', 'Đồ uống'],
            'trà' => ['Trà', 'Đồ uống'],
            'kem' => ['Bánh kem', 'Kem'],
            'chocolate' => ['Chocolate', 'Bánh ngọt'],
            'kẹo' => ['Kẹo', 'Bánh ngọt'],
        ];

        $mapped = 0;
        $products = \App\Models\MShopKeeperInventoryItem::whereNull('category_mshopkeeper_id')
            ->where('is_item', true)
            ->limit(1000) // Giới hạn để tránh timeout
            ->get();

        foreach ($products as $product) {
            $productName = strtolower($product->name);

            foreach ($mappingRules as $keyword => $possibleCategories) {
                if (str_contains($productName, $keyword)) {
                    // Tìm category phù hợp
                    foreach ($possibleCategories as $categoryName) {
                        if ($categoryMap->has($categoryName)) {
                            $category = $categoryMap->get($categoryName);
                            $product->update(['category_mshopkeeper_id' => $category->mshopkeeper_id]);
                            $mapped++;
                            break 2; // Break cả 2 vòng lặp
                        }
                    }
                }
            }
        }

        $this->info("✅ Mapped {$mapped} products to categories");
    }

    /**
     * Sync tất cả inventory items (method gốc, nhanh)
     */
    private function syncAllInventoryItems(float $startTime): array
    {
        $stats = [
            'total_api' => 0,
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'skipped' => 0,
            'stocks_created' => 0,
            'stocks_updated' => 0
        ];

        $allInventoryItems = [];
        $page = 1;
        $limit = 100;

        // Parameters for API call (sync tất cả - NHANH)
        $apiParams = [
            'Page' => $page,
            'Limit' => $limit,
            'SortField' => 'Name',
            'SortType' => 1,
            'IncludeInventory' => $this->option('include-inventory'),
            'IncludeInActive' => false
        ];

        // Lấy tất cả inventory items qua phân trang
        $maxPages = 100;

        do {
            $apiParams['Page'] = $page;

            $result = $this->mshopkeeperService->getInventoryItemsPagingWithDetail($apiParams);

            if (!$result['success']) {
                $this->error("❌ API call failed: " . ($result['error']['message'] ?? 'Unknown error'));
                break;
            }

            $inventoryItems = $result['data']['inventory_items'] ?? [];

            if (empty($inventoryItems)) {
                break;
            }

            $allInventoryItems = array_merge($allInventoryItems, $inventoryItems);
            $stats['total_api'] += count($inventoryItems);

            $this->info("📄 Page {$page}: " . count($inventoryItems) . " items");
            $page++;
        } while (count($inventoryItems) >= $limit && $page <= $maxPages);

        $this->info("📊 Total items from API: " . count($allInventoryItems));

        // Process items
        foreach ($allInventoryItems as $apiInventoryItem) {
            try {
                $result = $this->processInventoryItem($apiInventoryItem);
                $stats[$result['item_action']]++;

                if (isset($result['stock_stats'])) {
                    $stats['stocks_created'] += $result['stock_stats']['created'];
                    $stats['stocks_updated'] += $result['stock_stats']['updated'];
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("Error processing inventory item: " . $e->getMessage());
            }
        }

        return $stats;
    }





    /**
     * Process single inventory item
     */
    private function processInventoryItem(array $apiInventoryItem): array
    {
        $mshopkeeperId = $apiInventoryItem['Id'] ?? null;

        if (!$mshopkeeperId) {
            throw new \Exception('Inventory Item ID not found in API data');
        }

        $normalizedData = MShopKeeperInventoryItem::normalizeApiData($apiInventoryItem);

        $inventoryItem = MShopKeeperInventoryItem::where('mshopkeeper_id', $mshopkeeperId)->first();

        $itemAction = '';
        $stockStats = ['created' => 0, 'updated' => 0];

        // Remove nested transaction since we're already in a batch transaction
        if ($inventoryItem) {
            // Update existing - giữ nguyên is_visible và is_featured
            $updateData = $normalizedData;
            // Không ghi đè is_visible và is_featured để admin tự quản lý
            unset($updateData['is_visible']);
            unset($updateData['is_featured']);
            $inventoryItem->update($updateData);
            $inventoryItem->markAsSynced($apiInventoryItem); // Truyền dữ liệu đã modify
            $itemAction = 'updated';
        } else {
            // Create new - mặc định is_visible = true, is_featured = false
            $createData = $normalizedData;
            $createData['is_visible'] = true;
            $createData['is_featured'] = false;
            $inventoryItem = MShopKeeperInventoryItem::create($createData);
            $inventoryItem->markAsSynced($apiInventoryItem); // Truyền dữ liệu đã modify
            $itemAction = 'created';
        }

        // Process inventory stocks if available
        if (isset($apiInventoryItem['Inventories']) && is_array($apiInventoryItem['Inventories'])) {
            $stockStats = $this->processInventoryStocks($inventoryItem, $apiInventoryItem['Inventories']);
        }

        // Process child items if available
        if (isset($apiInventoryItem['ListDetail']) && is_array($apiInventoryItem['ListDetail'])) {
            foreach ($apiInventoryItem['ListDetail'] as $childItem) {
                $this->processChildInventoryItem($inventoryItem, $childItem);
            }
        }

        return [
            'item_action' => $itemAction,
            'stock_stats' => $stockStats
        ];
    }

    /**
     * Process inventory stocks for an item
     */
    private function processInventoryStocks(MShopKeeperInventoryItem $inventoryItem, array $inventories): array
    {
        $stats = ['created' => 0, 'updated' => 0];

        foreach ($inventories as $inventoryData) {
            $normalizedStockData = MShopKeeperInventoryStock::normalizeApiData($inventoryData);
            $normalizedStockData['inventory_item_id'] = $inventoryItem->id;

            $stock = MShopKeeperInventoryStock::where([
                'product_mshopkeeper_id' => $normalizedStockData['product_mshopkeeper_id'],
                'branch_mshopkeeper_id' => $normalizedStockData['branch_mshopkeeper_id']
            ])->first();

            if ($stock) {
                $stock->update($normalizedStockData);
                $stock->markAsSynced($inventoryData);
                $stats['updated']++;
            } else {
                $stock = MShopKeeperInventoryStock::create($normalizedStockData);
                $stock->markAsSynced($inventoryData);
                $stats['created']++;
            }
        }

        return $stats;
    }

    /**
     * Process child inventory item
     */
    private function processChildInventoryItem(MShopKeeperInventoryItem $parentItem, array $childData): void
    {
        $childId = $childData['Id'] ?? null;
        if (!$childId) return;

        $normalizedChildData = MShopKeeperInventoryItem::normalizeApiData($childData);
        $normalizedChildData['parent_id'] = $parentItem->id;
        $normalizedChildData['parent_mshopkeeper_id'] = $parentItem->mshopkeeper_id;

        $childItem = MShopKeeperInventoryItem::where('mshopkeeper_id', $childId)->first();

        if ($childItem) {
            // Update existing child - giữ nguyên is_visible và is_featured
            $updateChildData = $normalizedChildData;
            unset($updateChildData['is_visible']);
            unset($updateChildData['is_featured']);
            $childItem->update($updateChildData);
            $childItem->markAsSynced($childData);
        } else {
            // Create new child - mặc định is_visible = true, is_featured = false
            $createChildData = $normalizedChildData;
            $createChildData['is_visible'] = true;
            $createChildData['is_featured'] = false;
            $childItem = MShopKeeperInventoryItem::create($createChildData);
            $childItem->markAsSynced($childData);
        }

        // Process child item stocks
        if (isset($childData['Inventories']) && is_array($childData['Inventories'])) {
            $this->processInventoryStocks($childItem, $childData['Inventories']);
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
            ['Items Created', $stats['created']],
            ['Items Updated', $stats['updated']],
            ['Stocks Created', $stats['stocks_created']],
            ['Stocks Updated', $stats['stocks_updated']],
            ['Errors', $stats['errors']],
            ['Skipped', $stats['skipped']],
            ['Duration', $duration . 's'],
        ];

        $this->table($headers, $rows);

        // Log final stats
        Log::info('MShopKeeper Inventory Items Sync completed', [
            'stats' => $stats,
            'duration' => $duration
        ]);
    }
}
