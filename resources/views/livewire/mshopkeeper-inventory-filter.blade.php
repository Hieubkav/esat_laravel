{{-- Updated for smooth infinite scroll and spinner --}}
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-3 md:px-4 xl:px-3 py-6">
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
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Desktop Sidebar Filters -->
            <aside class="hidden lg:block lg:w-72 xl:w-72 flex-shrink-0">
                <div class="space-y-6" id="desktop-filter-content">
                    <!-- Search -->
                    <div class="filter-card rounded-xl p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Tìm kiếm</h3>
                        <div class="relative">
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="Nhập tên sản phẩm, mã hàng..."
                                   class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 font-open-sans text-sm">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <!-- DEBUG: Categories count: {{ count($categories ?? []) }} -->
                    @if(count($categories ?? []) > 0)
                    <div class="filter-card rounded-xl p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Danh mục</h3>
                        <div class="space-y-1.5 max-h-64 overflow-y-auto" x-data>
                            <button wire:click="$set('category', '')"
                                   class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ !$category ? 'active' : '' }}">
                                Tất cả danh mục
                            </button>
                            @php($hasTree = (is_array($categories ?? null)) && isset($categories[0]) && is_array($categories[0]) && array_key_exists('children', $categories[0]))
                            @if(($hasTree && count($categories ?? []) > 0))
                                @foreach($categories as $node)
                                    @include('livewire.partials.mshopkeeper-category-node', ['node' => $node, 'level' => 0, 'current' => ($category ?? '')])
                                @endforeach
                            @else
                            @foreach($categories ?? [] as $cat)
                                <button wire:click="$set('category', {{ json_encode($cat['value']) }})"
                                       class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ $category == $cat['value'] ? 'active' : '' }}">
                                    {{ $cat['label'] }}
                                    <span class="text-gray-400 text-xs">({{ $cat['count'] }})</span>
                                </button>
                            @endforeach
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Item Type Filter -->
                    <div class="filter-card rounded-xl p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Loại sản phẩm</h3>
                        <div class="space-y-1.5">
                            <button wire:click="$set('itemType', '')"
                                   class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ !($itemType ?? '') ? 'active' : '' }}">
                                Tất cả loại
                            </button>
                            @foreach(($this->itemTypes ?? []) as $type)
                                <button wire:click="$set('itemType', '{{ $type['value'] }}')"
                                       class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ ($itemType ?? '') == $type['value'] ? 'active' : '' }}">
                                    {{ $type['label'] }}
                                    <span class="text-gray-400 text-xs">({{ $type['count'] }})</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="filter-card rounded-xl p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Khoảng giá</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number"
                                   wire:model.live.debounce.500ms="minPrice"
                                   placeholder="Từ 0"
                                   class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm">
                            <input type="number"
                                   wire:model.live.debounce.500ms="maxPrice"
                                   placeholder="Đến..."
                                   class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm">
                        </div>
                    </div>

                    <!-- Special Filters -->
                    <div class="filter-card rounded-xl p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Đặc biệt</h3>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="inStock" class="sr-only">
                                <div class="relative">
                                    <div class="w-4 h-4 border-2 border-gray-300 rounded {{ ($inStock ?? false) ? 'bg-red-500 border-red-500' : '' }}"></div>
                                    @if($inStock ?? false)
                                        <i class="fas fa-check absolute top-0 left-0 w-4 h-4 text-white text-xs flex items-center justify-center"></i>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm text-gray-700 font-open-sans">Chỉ sản phẩm còn hàng</span>
                            </label>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    @if(($search ?? '') || ($itemType ?? '') || ($category ?? '') || ($sort ?? 'default') !== 'default' || ($minPrice ?? '') || ($maxPrice ?? '') || ($inStock ?? false))
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
            <div class="flex-1">
                <!-- Results Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-2xl font-bold text-gray-900 font-montserrat">
                            Kho Hàng Sản Phẩm
                        </h1>
                        <p class="text-gray-600 font-open-sans">
                            Tìm thấy {{ $this->totalProducts ?? 0 }} sản phẩm
                            @if($search ?? '')
                                cho từ khóa "<span class="font-medium">{{ $search }}</span>"
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700 font-open-sans">Sắp xếp:</label>
                        <select wire:model.live="sort"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-red-500 focus:border-red-500 font-open-sans">
                            @foreach(($sortOptions ?? []) as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Loading State -->
                <div wire:loading.delay wire:target="search,itemType,category,sort,minPrice,maxPrice,inStock,clearFilters" class="text-center py-8">
                    <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-blue-500 bg-blue-100">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang tải...
                    </div>
                </div>

                <!-- Products Grid -->
                <div wire:loading.remove wire:target="search,itemType,category,sort,minPrice,maxPrice,inStock,clearFilters">
                    @if(($this->products ?? collect())->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-5 xl:gap-5 mb-16">
                            @foreach(($this->products ?? []) as $product)
                                <article class="group" wire:key="product-{{ $product->id ?? $product->code }}">
                                    <a href="{{ route('mshopkeeper.inventory.show', $product->code) }}" class="block">
                                        <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                                            <!-- Product Image -->
                                            <div class="aspect-square overflow-hidden relative">
                                                @if($product->picture)
                                                    <img src="{{ $product->picture }}"
                                                         alt="{{ $product->name }}"
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                @else
                                                    <!-- Custom placeholder giống /danh-muc -->
                                                    <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 flex flex-col items-center justify-center relative overflow-hidden">
                                                        <div class="text-center">
                                                            <i class="fas fa-birthday-cake text-4xl text-red-300 mb-2"></i>
                                                            <p class="text-xs text-red-400 font-medium">Vũ Phúc Baking</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Badges -->
                                                <div class="absolute top-2 left-2 flex flex-col gap-1">
                                                    @if($product->total_on_hand > 0)
                                                        <span class="bg-red-50 text-red-700 text-xs px-2 py-0.5 rounded-full font-medium shadow-sm border border-red-100">Còn hàng</span>
                                                    @endif
                                                </div>
                                                @if($product->cost_price > 0 && $product->cost_price > $product->selling_price)
                                                    <div class="absolute top-2 right-2 bg-gradient-to-r from-red-500 to-red-600 text-white px-2 py-1 rounded-full text-xs font-bold shadow-lg">
                                                        -{{ round((($product->cost_price - $product->selling_price) / $product->cost_price) * 100) }}%
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Info -->
                                            <div class="p-4">
                                                <span class="text-xs text-red-500 font-medium uppercase tracking-wide mb-1 block">
                                                    {{ match($product->item_type) {
                                                        1 => 'Hàng Hoá',
                                                        2 => 'Combo',
                                                        4 => 'Dịch Vụ',
                                                        default => 'Khác'
                                                    } }}
                                                </span>
                                                <h3 class="text-sm md:text-base font-semibold text-gray-900 group-hover:text-red-700 transition-colors line-clamp-2 mb-3 font-montserrat">
                                                    {{ $product->name }}
                                                </h3>

                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        @if($product->cost_price > 0 && $product->cost_price > $product->selling_price)
                                                            <div class="flex flex-col">
                                                                <span class="text-red-600 font-bold text-sm md:text-base">{{ number_format($product->selling_price, 0, ',', '.') }}đ</span>
                                                                <span class="text-gray-400 line-through text-xs">{{ number_format($product->cost_price, 0, ',', '.') }}đ</span>
                                                            </div>
                                                        @else
                                                            <span class="text-red-600 font-bold text-sm md:text-base">{{ number_format($product->selling_price, 0, ',', '.') }}đ</span>
                                                        @endif
                                                    </div>
                                                    <span class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-red-50 to-red-100 px-2 py-1 text-[11px] leading-none font-medium text-red-700 group-hover:from-red-100 group-hover:to-red-200 transition-all">
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
                        @if($hasMoreProducts ?? false)
                            <!-- Infinite Scroll Trigger + Spinner -->
                            <div class="text-center mt-2">
                                <!-- Sentinel triggers when visible; hidden while loading to avoid double-fire -->
                                <div wire:loading.remove wire:target="loadMore" class="w-full">
                                    <div
                                        x-data
                                        x-init="
                                            const sentinel = $el;
                                            const observer = new IntersectionObserver((entries) => {
                                                if (entries[0].isIntersecting) {
                                                    $wire.loadMore();
                                                }
                                            }, { root: null, rootMargin: '400px 0px 400px 0px', threshold: 0.01 });
                                            observer.observe(sentinel);
                                            window.addEventListener('beforeunload', () => observer.disconnect());
                                        "
                                        class="h-1 w-full"></div>
                                </div>
                                <!-- Smooth loading spinner while fetching more -->
                                <div wire:loading wire:target="loadMore" class="inline-flex items-center px-4 py-2 text-gray-600 bg-white rounded-xl shadow-sm">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z"></path>
                                    </svg>
                                    <span class="text-sm">Đang tải thêm...</span>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-red-50 to-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-search text-4xl text-red-300"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2 font-montserrat">Không tìm thấy sản phẩm</h3>
                            <p class="text-gray-600 mb-6 font-open-sans">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm khác</p>
                            <button wire:click="clearFilters"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300">
                                <i class="fas fa-redo mr-2"></i>
                                Xóa bộ lọc
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

        <!-- Mobile Filter Sidebar -->
        <div x-data="{ open: false }"
             @toggle-mobile-filter.window="open = !open"
             class="lg:hidden">
            <!-- Overlay -->
            <div x-show="open"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false"
                 class="fixed inset-0 bg-black bg-opacity-25 z-40"></div>

            <!-- Sidebar -->
            <div x-show="open"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="fixed right-0 top-0 h-full w-full max-w-sm bg-white z-50 overflow-y-auto">

                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 font-montserrat">Bộ lọc & Tìm kiếm</h3>
                        <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Mobile Filter Content (copy from desktop) -->
                    <div class="space-y-6">
                        <!-- Search -->
                        <div class="filter-card rounded-xl p-5">
                            <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Tìm kiếm</h3>
                            <div class="relative">
                                <input type="text"
                                       wire:model.live.debounce.300ms="search"
                                       placeholder="Nhập tên sản phẩm, mã hàng..."
                                       class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 font-open-sans text-sm">
                                <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        @if(count($categories ?? []) > 0)
                        <div class="filter-card rounded-xl p-5">
                            <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Danh mục</h3>
                            <div class="space-y-1.5 max-h-48 overflow-y-auto">
                                <button wire:click="$set('category', '')"
                                       class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ !($category ?? '') ? 'active' : '' }}">
                                    Tất cả danh mục
                                </button>
                                @php($hasTree2 = (is_array($categories ?? null)) && isset($categories[0]) && is_array($categories[0]) && array_key_exists('children', $categories[0]))
                                @if(($hasTree2 && count($categories ?? []) > 0))
                                    @foreach($categories as $node)
                                        @include('livewire.partials.mshopkeeper-category-node', ['node' => $node, 'level' => 0, 'current' => ($category ?? '')])
                                    @endforeach
                                @else
                                    @foreach($categories ?? [] as $cat)
                                        <button wire:click="$set('category', {{ json_encode($cat['id'] ?? $cat['value']) }})"
                                               class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ ($category ?? '') == ($cat['id'] ?? $cat['value']) ? 'active' : '' }}">
                                            {{ $cat['label'] }}
                                            <span class="text-gray-400 text-xs">({{ $cat['total'] ?? $cat['count'] }})</span>
                                        </button>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Item Type Filter -->
                        <div class="filter-card rounded-xl p-5">
                            <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Loại sản phẩm</h3>
                            <div class="space-y-1.5">
                                <button wire:click="$set('itemType', '')"
                                       class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ !($itemType ?? '') ? 'active' : '' }}">
                                    Tất cả loại
                                </button>
                                @foreach(($this->itemTypes ?? []) as $type)
                                    <button wire:click="$set('itemType', '{{ $type['value'] }}')"
                                           class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ ($itemType ?? '') == $type['value'] ? 'active' : '' }}">
                                        {{ $type['label'] }}
                                        <span class="text-gray-400 text-xs">({{ $type['count'] }})</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="filter-card rounded-xl p-5">
                            <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Khoảng giá</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number"
                                       wire:model.live.debounce.500ms="minPrice"
                                       placeholder="Từ 0"
                                       class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm">
                                <input type="number"
                                       wire:model.live.debounce.500ms="maxPrice"
                                       placeholder="Đến..."
                                       class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm">
                            </div>
                        </div>

                        <!-- Special Filters -->
                        <div class="filter-card rounded-xl p-5">
                            <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Đặc biệt</h3>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.live="inStock" class="sr-only">
                                    <div class="relative">
                                        <div class="w-4 h-4 border-2 border-gray-300 rounded {{ ($inStock ?? false) ? 'bg-red-500 border-red-500' : '' }}"></div>
                                        @if($inStock ?? false)
                                            <i class="fas fa-check absolute top-0 left-0 w-4 h-4 text-white text-xs flex items-center justify-center"></i>
                                        @endif
                                    </div>
                                    <span class="ml-3 text-sm text-gray-700 font-open-sans">Chỉ sản phẩm còn hàng</span>
                                </label>
                            </div>
                        </div>

                        <!-- Clear Filters -->
                        @if(($search ?? '') || ($itemType ?? '') || ($category ?? '') || ($sort ?? 'default') !== 'default' || ($minPrice ?? '') || ($maxPrice ?? '') || ($inStock ?? false))
                        <div class="filter-card rounded-xl p-5">
                            <button wire:click="clearFilters"
                                   class="block w-full text-center px-3 py-2.5 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors font-medium font-open-sans text-sm">
                                <i class="fas fa-redo mr-2"></i>
                                Xóa bộ lọc
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
