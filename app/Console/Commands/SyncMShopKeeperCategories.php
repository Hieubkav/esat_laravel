<?php

namespace App\Console\Commands;

use App\Models\MShopKeeperCategory;
use App\Services\MShopKeeperService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncMShopKeeperCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mshopkeeper:sync-categories
                            {--force : Force sync even if recently synced}
                            {--dry-run : Show what would be synced without actually syncing}
                            {--clear : Clear all existing data before sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync categories from MShopKeeper API to database';

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
        $this->info('🚀 Starting MShopKeeper Categories Sync...');

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

            // Sync flat categories first
            $this->info('📋 Syncing flat categories...');
            $flatStats = $this->syncFlatCategories();
            foreach ($flatStats as $key => $value) {
                $stats[$key] += $value;
            }

            // Build parent-child relationships
            $this->info('🔗 Building parent-child relationships...');
            $this->buildRelationships();

            // Calculate grades based on tree depth
            $this->info('📊 Calculating category grades...');
            $this->calculateGrades();

            // Calculate is_leaf based on children count
            $this->info('🍃 Calculating leaf status...');
            $this->calculateLeafStatus();

            // Sync tree structure for validation
            $this->info('🌳 Validating tree structure...');
            $this->validateTreeStructure();

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            $this->displayResults($stats, $duration);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Sync failed: ' . $e->getMessage());
            Log::error('MShopKeeper Categories Sync failed', [
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
            $count = MShopKeeperCategory::count();
            if ($count > 0) {
                if ($this->confirm("⚠️  This will delete {$count} existing categories. Continue?")) {
                    MShopKeeperCategory::truncate();
                    $this->info("🗑️  Cleared {$count} existing categories");
                } else {
                    $this->info('❌ Sync cancelled');
                    exit(Command::FAILURE);
                }
            }
        } else {
            $count = MShopKeeperCategory::count();
            $this->info("🔍 [DRY RUN] Would clear {$count} existing categories");
        }
    }

    /**
     * Sync flat categories from API
     */
    private function syncFlatCategories(): array
    {
        $stats = ['total_api' => 0, 'created' => 0, 'updated' => 0, 'errors' => 0, 'skipped' => 0];

        // Get categories from API
        $result = $this->mshopkeeperService->getCategories();

        if (!$result['success']) {
            throw new \Exception('Failed to fetch categories from API: ' . ($result['error']['message'] ?? 'Unknown error'));
        }

        $apiCategories = $result['data']['categories'] ?? [];
        $stats['total_api'] = count($apiCategories);

        $this->info("📥 Found {$stats['total_api']} categories from API");

        if ($this->option('dry-run')) {
            $this->info('🔍 [DRY RUN] Would process categories...');
            return $stats;
        }

        $progressBar = $this->output->createProgressBar($stats['total_api']);
        $progressBar->start();

        foreach ($apiCategories as $apiCategory) {
            try {
                $result = $this->syncSingleCategory($apiCategory);
                $stats[$result]++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("\n❌ Error syncing category {$apiCategory['id']}: " . $e->getMessage());
                Log::error('Error syncing category', [
                    'category_id' => $apiCategory['id'],
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return $stats;
    }

    /**
     * Sync single category
     */
    private function syncSingleCategory(array $apiCategory): string
    {
        $mshopkeeperId = $apiCategory['id'];

        // Find existing category
        $category = MShopKeeperCategory::findByMShopKeeperId($mshopkeeperId);

        // Check if needs sync (skip if recently synced and not forced)
        if ($category && !$this->option('force') && !$category->needsSync()) {
            return 'skipped';
        }

        $categoryData = [
            'mshopkeeper_id' => $mshopkeeperId,
            'code' => $apiCategory['code'] ?? null,
            'name' => $apiCategory['name'] ?? 'Unnamed Category',
            'description' => $apiCategory['description'] ?? null,
            // Grade sẽ được tính toán sau khi build relationships
            'grade' => 0, // Temporary value, will be calculated later
            'inactive' => ($apiCategory['status'] ?? 'active') === 'inactive',
            // is_leaf sẽ được tính toán sau khi build relationships
            'is_leaf' => false, // Temporary value, will be calculated later
            'parent_mshopkeeper_id' => $apiCategory['parent_id'] ?? null,
            'sort_order' => $apiCategory['sort_order'] ?? 0,
            'raw_data' => $apiCategory,
        ];

        if ($category) {
            // Update existing
            $category->update($categoryData);
            $category->markAsSynced($apiCategory);
            return 'updated';
        } else {
            // Create new
            $category = MShopKeeperCategory::create($categoryData);
            $category->markAsSynced($apiCategory);
            return 'created';
        }
    }

    /**
     * Build parent-child relationships
     */
    private function buildRelationships(): void
    {
        $categories = MShopKeeperCategory::whereNotNull('parent_mshopkeeper_id')->get();
        $updated = 0;

        foreach ($categories as $category) {
            $parent = MShopKeeperCategory::findByMShopKeeperId($category->parent_mshopkeeper_id);

            if ($parent && $category->parent_id !== $parent->id) {
                $category->update(['parent_id' => $parent->id]);
                $updated++;
            }
        }

        $this->info("🔗 Updated {$updated} parent-child relationships");
    }

    /**
     * Calculate grades based on tree depth
     */
    private function calculateGrades(): void
    {
        $updated = 0;

        // Get all categories with parent relationship loaded
        $categories = MShopKeeperCategory::with('parent')->get();

        foreach ($categories as $category) {
            $depth = $this->calculateCategoryDepth($category, $categories);

            if ($category->grade !== $depth) {
                $category->update(['grade' => $depth]);
                $updated++;
            }
        }

        $this->info("📊 Updated {$updated} category grades");
    }

    /**
     * Calculate category depth in tree (0-based)
     */
    private function calculateCategoryDepth(MShopKeeperCategory $category, \Illuminate\Database\Eloquent\Collection $allCategories): int
    {
        $depth = 0;
        $currentId = $category->parent_id;

        // Traverse up the tree to count depth
        while ($currentId) {
            $depth++;

            // Find parent in the collection to avoid additional queries
            $parent = $allCategories->firstWhere('id', $currentId);
            if (!$parent) {
                break;
            }

            $currentId = $parent->parent_id;

            // Prevent infinite loops
            if ($depth > 10) {
                $this->warn("⚠️  Possible circular reference detected for category {$category->id}");
                break;
            }
        }

        return $depth;
    }

    /**
     * Calculate is_leaf based on children count
     */
    private function calculateLeafStatus(): void
    {
        $updated = 0;

        // Get all categories with children count
        $categories = MShopKeeperCategory::withCount('children')->get();

        foreach ($categories as $category) {
            $shouldBeLeaf = $category->children_count === 0;

            if ($category->is_leaf !== $shouldBeLeaf) {
                $category->update(['is_leaf' => $shouldBeLeaf]);
                $updated++;
            }
        }

        $this->info("🍃 Updated {$updated} leaf status");
    }

    /**
     * Validate tree structure
     */
    private function validateTreeStructure(): void
    {
        $result = $this->mshopkeeperService->getCategoriesTree();

        if ($result['success']) {
            $treeData = $result['data']['categories_tree'] ?? [];
            $treeCount = $this->countTreeNodes($treeData);
            $dbCount = MShopKeeperCategory::count();

            $this->info("🌳 Tree validation: API={$treeCount}, DB={$dbCount}");

            if ($treeCount !== $dbCount) {
                $this->warn("⚠️  Tree count mismatch! Some categories may be missing.");
            }
        }
    }

    /**
     * Count nodes in tree structure
     */
    private function countTreeNodes(array $nodes): int
    {
        $count = count($nodes);

        foreach ($nodes as $node) {
            if (!empty($node['Children'])) {
                $count += $this->countTreeNodes($node['Children']);
            }
        }

        return $count;
    }

    /**
     * Display sync results
     */
    private function displayResults(array $stats, float $duration): void
    {
        $this->newLine();
        $this->info('✅ Sync completed successfully!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['API Categories', $stats['total_api']],
                ['Created', $stats['created']],
                ['Updated', $stats['updated']],
                ['Skipped', $stats['skipped']],
                ['Errors', $stats['errors']],
                ['Duration', $duration . 's'],
            ]
        );

        // Show sync stats
        $syncStats = MShopKeeperCategory::getSyncStats();
        $this->newLine();
        $this->info('📊 Database Statistics:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Categories', $syncStats['total']],
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
