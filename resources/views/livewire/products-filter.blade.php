<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Mobile Filter Button -->
        <div class="lg:hidden mb-6">
            <button @click="$dispatch('toggle-mobile-filter')"
                    class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                <span class="flex items-center font-medium">
                    <i class="fas fa-filter mr-2 text-red-500"></i>
                    Bộ lọc & Tìm kiếm
                </span>
                <i class="fas fa-chevron-down text-gray-400"></i>
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Desktop Sidebar Filters -->
            <aside class="hidden lg:block w-80 flex-shrink-0">
                <div class="space-y-6" id="desktop-filter-content">
                    <!-- Search -->
                    <div class="filter-card rounded-xl p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Tìm kiếm</h3>
                        <div class="relative">
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="Nhập tên sản phẩm..."
                                   class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 font-open-sans text-sm">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="filter-card rounded-xl p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Danh mục</h3>
                        <div class="space-y-1.5">
                            <button wire:click="$set('category', '')"
                                   class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ !$category ? 'active' : '' }}">
                                Tất cả danh mục
                            </button>
                            @foreach($this->categories as $cat)
                                <button wire:click="$set('category', '{{ $cat->id }}')"
                                       class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ $category == $cat->id ? 'active' : '' }}">
                                    {{ $cat->name }}
                                    <span class="text-gray-400 text-xs">({{ $cat->products_count }})</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    @if($this->priceRange && $this->priceRange->min_price && $this->priceRange->max_price)
                    <div class="filter-card rounded-xl p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Khoảng giá</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number"
                                   wire:model.live.debounce.500ms="minPrice"
                                   placeholder="Từ {{ number_format($this->priceRange->min_price, 0, ',', '.') }}"
                                   class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm">
                            <input type="number"
                                   wire:model.live.debounce.500ms="maxPrice"
                                   placeholder="Đến {{ number_format($this->priceRange->max_price, 0, ',', '.') }}"
                                   class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm">
                        </div>
                    </div>
                    @endif

                    <!-- Special Filters -->
                    <div class="filter-card rounded-xl p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Đặc biệt</h3>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="isHot" class="sr-only">
                                <div class="relative">
                                    <div class="w-4 h-4 border-2 border-gray-300 rounded {{ $isHot ? 'bg-red-500 border-red-500' : '' }}"></div>
                                    @if($isHot)
                                        <i class="fas fa-check absolute top-0 left-0 w-4 h-4 text-white text-xs flex items-center justify-center"></i>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm text-gray-700 font-open-sans">Sản phẩm nổi bật</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="hasDiscount" class="sr-only">
                                <div class="relative">
                                    <div class="w-4 h-4 border-2 border-gray-300 rounded {{ $hasDiscount ? 'bg-red-500 border-red-500' : '' }}"></div>
                                    @if($hasDiscount)
                                        <i class="fas fa-check absolute top-0 left-0 w-4 h-4 text-white text-xs flex items-center justify-center"></i>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm text-gray-700 font-open-sans">Đang giảm giá</span>
                            </label>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    @if($search || $category || $sort !== 'newest' || $minPrice || $maxPrice || $isHot || $hasDiscount)
                    <div class="filter-card rounded-xl p-5">
                        <button wire:click="clearFilters"
                               class="block w-full text-center px-3 py-2.5 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors font-medium font-open-sans text-sm">
                            <i class="fas fa-redo mr-2"></i>
                            Xóa bộ lọc
                        </button>
                    </div>
                    @endif
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                <!-- Active Filters & Results Count -->
                <div class="mb-6">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        @if($search)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-red-100 text-red-800">
                                <i class="fas fa-search mr-1.5"></i>
                                "{{ $search }}"
                                <button wire:click="$set('search', '')" class="ml-2 hover:text-red-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif
                        @if($category)
                            @php $selectedCategory = $this->categories->find($category); @endphp
                            @if($selectedCategory)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-blue-100 text-blue-800">
                                    <i class="fas fa-tag mr-1.5"></i>
                                    {{ $selectedCategory->name }}
                                    <button wire:click="$set('category', '')" class="ml-2 hover:text-blue-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            @endif
                        @endif
                        @if($isHot)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-orange-100 text-orange-800">
                                <i class="fas fa-fire mr-1.5"></i>
                                Nổi bật
                                <button wire:click="$set('isHot', false)" class="ml-2 hover:text-orange-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif
                        @if($hasDiscount)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-green-100 text-green-800">
                                <i class="fas fa-percent mr-1.5"></i>
                                Giảm giá
                                <button wire:click="$set('hasDiscount', false)" class="ml-2 hover:text-green-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600 font-open-sans">
                            <span wire:loading.remove>
                                Hiển thị {{ count($this->products) }} sản phẩm
                            </span>
                            <span wire:loading class="flex items-center">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Đang tải...
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-500 font-open-sans">Sắp xếp:</label>
                            <select wire:model.live="sort"
                                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-red-500 focus:border-red-500 bg-white">
                                @foreach($sortOptions as $sortKey => $sortName)
                                    <option value="{{ $sortKey }}">{{ $sortName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div wire:loading.remove>
                    @if($this->products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-16">
                            @foreach($this->products as $product)
                                <article class="group">
                                    <a href="{{ route('products.show', $product->slug) }}" class="block">
                                        <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                                            <!-- Product Image -->
                                            <div class="aspect-square overflow-hidden relative">
                                                @php
                                                    $image = collect($product->product_images ?? [])->where('status', 'active')->sortBy('order')->first();
                                                @endphp
                                                @if($image && isset($image['image_link']))
                                                    <img src="{{ asset('storage/' . $image['image_link']) }}"
                                                         alt="{{ $product->name }}"
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                @else
                                                    <!-- Custom placeholder -->
                                                    <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 flex flex-col items-center justify-center relative overflow-hidden">
                                                        <div class="text-center">
                                                            <i class="fas fa-birthday-cake text-4xl text-red-300 mb-2"></i>
                                                            <p class="text-xs text-red-400 font-medium">Vũ Phúc Baking</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Badges -->
                                                <div class="absolute top-2 left-2 flex flex-col gap-1">
                                                    @if($product->is_hot)
                                                        <span class="bg-gradient-to-r from-orange-400 to-orange-500 text-white text-xs px-2 py-1 rounded-full font-bold shadow-lg">HOT</span>
                                                    @endif
                                                </div>
                                                @if(isset($product->compare_price) && $product->compare_price > $product->price)
                                                    <div class="absolute top-2 right-2 bg-gradient-to-r from-red-500 to-red-600 text-white px-2 py-1 rounded-full text-xs font-bold shadow-lg">
                                                        -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Info -->
                                            <div class="p-4">
                                                @if(isset($product->category) && $product->category)
                                                    <span class="text-xs text-red-500 font-medium uppercase tracking-wide mb-1 block">
                                                        {{ is_object($product->category) ? $product->category->name : $product->category['name'] }}
                                                    </span>
                                                @endif
                                                <h3 class="text-sm md:text-base font-semibold text-gray-900 group-hover:text-red-700 transition-colors line-clamp-2 mb-3 font-montserrat">
                                                    {{ $product->name }}
                                                </h3>

                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        @if(isset($product->compare_price) && $product->compare_price > $product->price)
                                                            <div class="flex flex-col">
                                                                <span class="text-red-600 font-bold text-sm md:text-base">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                                                <span class="text-gray-400 line-through text-xs">{{ number_format($product->compare_price, 0, ',', '.') }}đ</span>
                                                            </div>
                                                        @else
                                                            <span class="text-red-600 font-bold text-sm md:text-base">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                                        @endif
                                                    </div>
                                                    <span class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-red-50 to-red-100 px-3 py-1.5 text-xs font-medium text-red-700 group-hover:from-red-100 group-hover:to-red-200 transition-all">
                                                        Chi tiết
                                                        <i class="fas fa-arrow-right ml-1"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>

                        <!-- Load More Button -->
                        @if($hasMoreProducts)
                            <div class="text-center">
                                <button wire:click="loadMore"
                                        class="inline-flex items-center px-8 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-medium shadow-sm"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="loadMore">
                                        <i class="fas fa-plus mr-2"></i>
                                        Xem thêm sản phẩm
                                    </span>
                                    <span wire:loading wire:target="loadMore" class="flex items-center">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Đang tải...
                                    </span>
                                </button>
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div class="max-w-md mx-auto">
                                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-search text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3 font-montserrat">Không tìm thấy sản phẩm</h3>
                                <p class="text-gray-600 mb-6 font-open-sans">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm để xem thêm kết quả.</p>
                                <button wire:click="clearFilters"
                                       class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium font-open-sans">
                                    <i class="fas fa-redo mr-2"></i>
                                    Xem tất cả sản phẩm
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Loading State -->
                <div wire:loading class="text-center py-16">
                    <div class="inline-flex items-center px-6 py-3 bg-white rounded-xl shadow-sm">
                        <i class="fas fa-spinner fa-spin text-red-500 mr-3"></i>
                        <span class="text-gray-700 font-medium">Đang tải sản phẩm...</span>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

@push('styles')
<style>
    .filter-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .filter-btn {
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        background-color: #fef2f2;
        color: #dc2626;
    }

    .filter-btn.active {
        background-color: #dc2626;
        color: white;
    }

    .product-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover {
        transform: translateY(-4px);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
