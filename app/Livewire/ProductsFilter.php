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
    ];

    public function mount($selectedCategory = null)
    {
        $this->search = request('search', '');
        $this->sort = request('sort', 'newest');

        // Ưu tiên selectedCategory từ route, sau đó mới đến query string
        if ($selectedCategory) {
            $this->category = $selectedCategory->id;
        } else {
            $this->category = request('category', '');
        }

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

    public function loadMore()
    {
        $this->loadProducts();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->sort = 'newest';
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
            ->whereNotNull('slug')
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
                ->withCount(['products' => function($query) {
                    $query->where('status', 'active');
                }])
                ->orderBy('order')
                ->get();
        });
    }

    public function render()
    {
        return view('livewire.products-filter');
    }
}
