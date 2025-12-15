<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\CatProduct;
use Illuminate\Support\Facades\Cache;

class ProductsFilter extends Component
{
    public $search = '';
    public $category = '';
    public $sort = 'newest';
    public $minPrice = '';
    public $maxPrice = '';
    public $isHot = false;
    public $hasDiscount = false;
    public $perPage = 12;
    public $loadedProducts = [];
    public $hasMoreProducts = true;

    public $sortOptions = [
        'newest' => 'Mới nhất',
        'popular' => 'Phổ biến',
        'name_asc' => 'Tên A-Z',
        'name_desc' => 'Tên Z-A',
        'price_asc' => 'Giá thấp đến cao',
        'price_desc' => 'Giá cao đến thấp',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'sort' => ['except' => 'newest'],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'isHot' => ['except' => false],
        'hasDiscount' => ['except' => false],
    ];

    public function mount()
    {
        $this->search = request('search', '');
        $this->category = request('category', '');
        $this->sort = request('sort', 'newest');
        $this->minPrice = request('minPrice', '');
        $this->maxPrice = request('maxPrice', '');
        $this->isHot = request('isHot', false);
        $this->hasDiscount = request('hasDiscount', false);
        $this->loadProducts();
    }

    public function updatedSearch()
    {
        $this->resetProducts();
    }

    public function updatedCategory()
    {
        $this->resetProducts();
    }

    public function updatedSort()
    {
        $this->resetProducts();
    }

    public function updatedMinPrice()
    {
        $this->resetProducts();
    }

    public function updatedMaxPrice()
    {
        $this->resetProducts();
    }

    public function updatedIsHot()
    {
        $this->resetProducts();
    }

    public function updatedHasDiscount()
    {
        $this->resetProducts();
    }

    public function loadMore()
    {
        $this->loadProducts();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->sort = 'newest';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->isHot = false;
        $this->hasDiscount = false;
        $this->resetProducts();
    }

    private function resetProducts()
    {
        $this->loadedProducts = [];
        $this->hasMoreProducts = true;
        $this->loadProducts();
    }

    private function loadProducts()
    {
        $query = $this->getQuery();

        $newProducts = $query->skip(count($this->loadedProducts))
            ->take($this->perPage)
            ->get();

        if ($newProducts->count() < $this->perPage) {
            $this->hasMoreProducts = false;
        }

        $this->loadedProducts = array_merge($this->loadedProducts, $newProducts->toArray());
    }

    private function getQuery()
    {
        $query = Product::where('status', 'active')
            ->with(['category', 'productImages' => function($query) {
                $query->where('status', 'active')->orderBy('order');
            }]);

        // Lọc theo danh mục
        if ($this->category && $this->category !== 'all') {
            $query->where('category_id', $this->category);
        }

        // Tìm kiếm theo từ khóa
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Lọc theo giá
        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Lọc sản phẩm nổi bật
        if ($this->isHot) {
            $query->where('is_hot', true);
        }

        // Lọc sản phẩm giảm giá
        if ($this->hasDiscount) {
            $query->whereNotNull('compare_price')
                  ->whereColumn('compare_price', '>', 'price');
        }

        // Sắp xếp
        switch ($this->sort) {
            case 'popular':
                $query->orderBy('is_hot', 'desc')->orderBy('order');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }

    public function getProductsProperty()
    {
        return collect($this->loadedProducts)->map(function ($product) {
            // Convert array back to object with proper relations
            $productObj = (object) $product;

            // Handle product_images relation
            if (isset($product['product_images'])) {
                $productObj->product_images = collect($product['product_images']);
            }

            return $productObj;
        });
    }

    public function getCategoriesProperty()
    {
        return Cache::remember('products_categories_filter', 1800, function () {
            return CatProduct::where('status', 'active')
                ->whereNull('parent_id')
                ->withCount(['products' => function($query) {
                    $query->where('status', 'active');
                }])
                ->orderBy('order')
                ->get();
        });
    }

    public function getPriceRangeProperty()
    {
        return Cache::remember('products_price_range', 1800, function () {
            return Product::where('status', 'active')
                ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                ->first();
        });
    }

    public function render()
    {
        return view('livewire.products-filter');
    }
}
