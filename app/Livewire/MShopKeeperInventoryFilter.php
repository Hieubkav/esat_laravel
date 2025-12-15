<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MShopKeeperInventoryItem;
use App\Models\MShopKeeperCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MShopKeeperInventoryFilter extends Component
{
    // Khai báo tất cả properties với giá trị mặc định để tránh PropertyNotFoundException
    public $search = '';
    public $itemType = '';
    public $category = '';
    public $sort = 'default';
    public $minPrice = '';
    public $maxPrice = '';
    public $inStock = false;
    public $perPage = 12;
    public $loadedProducts = [];
    public $hasMoreProducts = true;

    // Thêm property backup để tránh lỗi PropertyNotFoundException
    public $categories = [];

    // Thêm protected properties để Livewire có thể track
    protected $listeners = ['refreshComponent' => '$refresh'];

    public $sortOptions = [
        'default' => 'Mặc định',
        'name_asc' => 'Tên A-Z',
        'name_desc' => 'Tên Z-A',
        'price_asc' => 'Giá thấp đến cao',
        'price_desc' => 'Giá cao đến thấp',
        'stock_desc' => 'Tồn kho nhiều nhất',
        'newest' => 'Mới nhất',
    ];

    public $itemTypeOptions = [
        '' => 'Tất cả loại',
        '1' => 'Hàng Hoá',
        '2' => 'Combo',
        '4' => 'Dịch Vụ',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'itemType' => ['except' => ''],
        'category' => ['except' => ''],
        'sort' => ['except' => 'default'],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'inStock' => ['except' => false],
    ];

    /**
     * Đảm bảo tất cả properties được khởi tạo đúng cách
     */
    public function hydrate()
    {
        // Đảm bảo các properties luôn có giá trị hợp lệ
        $this->itemType = $this->itemType ?? '';
        $this->category = $this->category ?? '';
        $this->search = $this->search ?? '';
        $this->sort = $this->sort ?? 'default';
        $this->minPrice = $this->minPrice ?? '';
        $this->maxPrice = $this->maxPrice ?? '';
        $this->inStock = $this->inStock ?? false;
        $this->perPage = $this->perPage ?? 12;
        $this->loadedProducts = $this->loadedProducts ?? [];
        $this->hasMoreProducts = $this->hasMoreProducts ?? true;
        $this->categories = $this->categories ?? [];
    }

    public function mount()
    {
        try {
            // Already initialized by mount with hierarchical tree
            if (!empty($this->categories)) {
                return;
            }
            // If categories already initialized (built earlier), skip
            if (!empty($this->categories)) {
                return;
            }
            // If categories table is present, build hierarchical tree and return
            if (Schema::hasTable('mshopkeeper_categories')) {
                $tree = Cache::remember('mshopkeeper_categories_tree_with_counts', 1800, function () {
                    $productCounts = MShopKeeperInventoryItem::where('inactive', false)
                        ->where('is_visible', true)
                        ->where('is_item', true)
                        ->whereNotNull('category_mshopkeeper_id')
                        ->selectRaw('category_mshopkeeper_id, COUNT(*) as cnt')
                        ->groupBy('category_mshopkeeper_id')
                        ->get()
                        ->pluck('cnt', 'category_mshopkeeper_id');

                    $all = \App\Models\MShopKeeperCategory::active()
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get(['id', 'mshopkeeper_id', 'name', 'parent_id', 'grade']);

                    $byParent = $all->groupBy('parent_id');

                    $build = function ($cat) use (&$build, $byParent, $productCounts) {
                        $children = ($byParent[$cat->id] ?? collect())->map(function ($c) use (&$build) {
                            return $build($c);
                        })->filter()->values()->toArray();

                        $own = (int) ($productCounts[$cat->mshopkeeper_id] ?? 0);
                        $totalChildren = array_sum(array_map(function ($n) { return (int) ($n['total'] ?? 0); }, $children));
                        $total = $own + $totalChildren;

                        if ($total === 0) {
                            return null; // prune branches without products
                        }

                        return [
                            'id' => (string) $cat->mshopkeeper_id,
                            'label' => $cat->name,
                            'count' => $own,
                            'total' => $total,
                            'grade' => (int) $cat->grade,
                            'children' => $children,
                        ];
                    };

                    $roots = $byParent[null] ?? collect();
                    return $roots->map(function ($root) use ($build) {
                        return $build($root);
                    })->filter()->values()->toArray();
                });

                $this->categories = $tree;
                Log::info('Hierarchical categories initialized: ' . count($tree));
            }
            $this->search = request('search', '');
            $this->itemType = request('itemType', '');
            $this->category = request('category', '');
            $this->sort = request('sort', 'default');
            $this->minPrice = request('minPrice', '');
            $this->maxPrice = request('maxPrice', '');
            $this->inStock = request('inStock', false);

            // Xử lý parameter 'type' từ form cũ và convert sang itemType
            if (request('type')) {
                $typeMapping = [
                    'hang-hoa' => '1',
                    'combo' => '2',
                    'dich-vu' => '4',
                ];
                $this->itemType = $typeMapping[request('type')] ?? '';
            }

            // Khởi tạo categories với fallback an toàn
            $this->initializeCategories();

            // Load products với error handling
            $this->loadInitialProductsSafely();

            Log::info('MShopKeeper component mounted successfully');
        } catch (\Throwable $e) {
            Log::error('Error in MShopKeeper mount: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Fallback an toàn
            $this->loadedProducts = [];
            $this->hasMoreProducts = false;
            $this->categories = [];
        }
    }

    public function updated($propertyName)
    {
        // Reset products when filters change
        if (in_array($propertyName, ['search', 'itemType', 'category', 'sort', 'minPrice', 'maxPrice', 'inStock'])) {
            $this->resetProducts();
        }
    }

    public function resetProducts()
    {
        $this->loadedProducts = [];
        $this->hasMoreProducts = true;
        $this->loadInitialProducts();
    }

    public function loadMore()
    {
        if (!$this->hasMoreProducts) {
            return;
        }

        $currentCount = count($this->loadedProducts);
        $products = $this->getQuery()
            ->skip($currentCount)
            ->take($this->perPage)
            ->get()
            ->toArray();

        if (count($products) < $this->perPage) {
            $this->hasMoreProducts = false;
        }

        $this->loadedProducts = array_merge($this->loadedProducts, $products);
    }

    public function loadInitialProducts()
    {
        $products = $this->getQuery()
            ->take($this->perPage)
            ->get()
            ->toArray();

        $this->loadedProducts = $products;
        $this->hasMoreProducts = count($products) >= $this->perPage;
    }

    /**
     * Load products với error handling an toàn
     */
    private function loadInitialProductsSafely()
    {
        try {
            // Kiểm tra table tồn tại trước
            if (!Schema::hasTable('mshopkeeper_inventory_items')) {
                Log::error('Table mshopkeeper_inventory_items not found');
                $this->loadedProducts = [];
                $this->hasMoreProducts = false;
                return;
            }

            // Kiểm tra có data không
            $totalCount = MShopKeeperInventoryItem::where('inactive', false)
                ->where('is_visible', true)
                ->where('is_item', true)
                ->count();

            if ($totalCount === 0) {
                Log::warning('No products found in database');
                $this->loadedProducts = [];
                $this->hasMoreProducts = false;
                return;
            }

            // Load products bình thường
            $this->loadInitialProducts();

            Log::info('Products loaded successfully: ' . count($this->loadedProducts));

        } catch (\Throwable $e) {
            Log::error('Error loading initial products: ' . $e->getMessage());
            $this->loadedProducts = [];
            $this->hasMoreProducts = false;
        }
    }

    private function getQuery()
    {
        $query = MShopKeeperInventoryItem::where('inactive', false)
            ->where('is_visible', true)
            ->where('is_item', true)
            ->with(['stocks']);

        // Lọc theo loại sản phẩm
        if ($this->itemType && $this->itemType !== '') {
            $query->where('item_type', $this->itemType);
        }

        // Lọc theo danh mục
        if ($this->category && $this->category !== '') {
            $ids = $this->getDescendantCategoryIdsCached((string) $this->category);
            if (!empty($ids)) {
                $query->whereIn('category_mshopkeeper_id', $ids);
            } else {
                /*
                // Fallback theo tn danh mc (ca3u hcnh ce3)
                */
                $query->where('category_name', $this->category);
            }
        }

        // Tìm kiếm theo từ khóa
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('barcode', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Lọc theo giá
        if ($this->minPrice) {
            $query->where('selling_price', '>=', $this->minPrice);
        }
        if ($this->maxPrice) {
            $query->where('selling_price', '<=', $this->maxPrice);
        }

        // Lọc sản phẩm còn hàng
        if ($this->inStock) {
            $query->where('total_on_hand', '>', 0);
        }

        // Sắp xếp
        switch ($this->sort) {
            case 'price_asc':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'stock_desc':
                $query->orderBy('total_on_hand', 'desc');
                break;
            default:
                $query->orderBy('total_on_hand', 'desc')
                      ->orderBy('selling_price', 'desc')
                      ->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function getTotalProductsProperty()
    {
        return $this->getQuery()->count();
    }

    public function getProductsProperty()
    {
        return collect($this->loadedProducts)->map(function ($product) {
            // Convert array back to object with proper relations
            $productObj = (object) $product;

            // Handle stocks relation
            if (isset($product['stocks'])) {
                $productObj->stocks = collect($product['stocks']);
            }

            return $productObj;
        });
    }

    public function getItemTypesProperty()
    {
        return Cache::remember('mshopkeeper_item_types_filter', 1800, function () {
            return [
                ['value' => '1', 'label' => 'Hàng Hoá', 'count' => MShopKeeperInventoryItem::active()->visible()->where('is_item', true)->byItemType(1)->count()],
                ['value' => '2', 'label' => 'Combo', 'count' => MShopKeeperInventoryItem::active()->visible()->where('is_item', true)->byItemType(2)->count()],
                ['value' => '4', 'label' => 'Dịch Vụ', 'count' => MShopKeeperInventoryItem::active()->visible()->where('is_item', true)->byItemType(4)->count()],
            ];
        });
    }

    public function getCategoriesProperty()
    {
        // Luôn return data đã có sẵn từ mount()
        // Không query trong computed property để tránh lỗi
        return $this->categories ?? [];
    }

    /**
     * Khởi tạo categories an toàn trong mount
     */
    private function initializeCategories()
    {
        try {
            if (!empty($this->categories)) {
                // Already initialized earlier (e.g., hierarchical tree in mount)
                return;
            }
            // Already initialized by mount with hierarchical tree
            if (!empty($this->categories)) {
                return;
            }
            // Kiểm tra table tồn tại trước
            if (!Schema::hasTable('mshopkeeper_inventory_items')) {
                Log::error('Table mshopkeeper_inventory_items not found on production');
                $this->categories = $this->getFallbackCategories();
                return;
            }

            // Query đơn giản với limit
            $categories = MShopKeeperInventoryItem::whereNotNull('category_name')
                ->where('inactive', false)
                ->where('is_visible', true)
                ->where('is_item', true)
                ->selectRaw('category_name, COUNT(*) as count')
                ->groupBy('category_name')
                ->orderBy('category_name')
                ->limit(50) // Giảm limit để an toàn hơn
                ->get()
                ->map(function($item) {
                    return [
                        'value' => $item->category_name,
                        'label' => $item->category_name,
                        'count' => $item->count
                    ];
                })
                ->toArray();

            $this->categories = $categories;
            Log::info('Categories initialized successfully: ' . count($categories));

        } catch (\Throwable $e) {
            Log::error('Error initializing categories: ' . $e->getMessage());
            $this->categories = $this->getFallbackCategories();
        }
    }

    /**
     * Fallback categories khi có lỗi
     */
    private function getFallbackCategories()
    {
        return [
            ['value' => '', 'label' => 'Tất cả danh mục', 'count' => 0]
        ];
    }

    /**
     * Tạo mapping từ category name -> breadcrumb từ cấu trúc cây
     */
    private function buildCategoryBreadcrumbMap(): array
    {
        $mapping = [];

        // Lấy tất cả categories từ database (đã sync từ API tree)
        $categories = \App\Models\MShopKeeperCategory::with('parent.parent.parent')
            ->where('inactive', false)
            ->get();

        foreach ($categories as $category) {
            $breadcrumb = $this->buildBreadcrumbForCategory($category);
            $mapping[$category->name] = $breadcrumb;
        }

        return $mapping;
    }

    /**
     * Tạo breadcrumb cho một category
     */
    private function buildBreadcrumbForCategory($category): string
    {
        $breadcrumbs = [];
        $current = $category;

        // Traverse up the parent chain
        while ($current) {
            array_unshift($breadcrumbs, $current->name);
            $current = $current->parent;
        }

        return implode(' > ', $breadcrumbs);
    }

    // Get all descendant category_mshopkeeper_id including the given one (flat list)
    private function getDescendantCategoryIdsCached(string $mshopkeeperId): array
    {
        return Cache::remember('mshopkeeper_cat_desc_' . $mshopkeeperId, 1800, function () use ($mshopkeeperId) {
            try {
                if (!Schema::hasTable('mshopkeeper_categories')) {
                    return [];
                }

                $all = \App\Models\MShopKeeperCategory::active()
                    ->get(['id', 'mshopkeeper_id', 'parent_id']);

                $byParent = $all->groupBy('parent_id');
                $byExternal = $all->keyBy('mshopkeeper_id');
                $start = $byExternal[$mshopkeeperId] ?? null;
                if (!$start) {
                    return [];
                }

                $result = [];
                $stack = [$start];
                while (!empty($stack)) {
                    /** @var \App\Models\MShopKeeperCategory $node */
                    $node = array_pop($stack);
                    $result[] = (string) $node->mshopkeeper_id;
                    foreach (($byParent[$node->id] ?? collect()) as $child) {
                        $stack[] = $child;
                    }
                }

                return array_values(array_unique($result));
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->itemType = '';
        $this->category = '';
        $this->sort = 'default';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->inStock = false;

        $this->resetProducts();
    }

    public function render()
    {
        // Debug info
        Log::info('Render called - Products count: ' . count($this->loadedProducts));
        Log::info('Categories count: ' . count($this->categories));

        return view('livewire.mshopkeeper-inventory-filter');
    }
}
