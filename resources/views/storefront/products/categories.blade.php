@extends('layouts.shop')

@section('title', 'Tất cả danh mục sản phẩm - Vũ Phúc Baking')
@section('description', 'Khám phá tất cả danh mục sản phẩm nguyên liệu làm bánh, dụng cụ và thiết bị chuyên nghiệp tại Vũ Phúc Baking. Nhà phân phối uy tín khu vực ĐBSCL.')

@push('styles')
<style>
    .hero-pattern {
        background-image:
            radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 2px, transparent 2px),
            radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 2px, transparent 2px);
        background-size: 50px 50px;
    }

    .product-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .filter-card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    .filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 40;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .filter-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .filter-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 320px;
        height: 100vh;
        z-index: 50;
        transition: left 0.3s ease;
        overflow-y: auto;
    }

    .filter-sidebar.active {
        left: 0;
    }

    .filter-sidebar-desktop {
        position: static !important;
        width: auto !important;
        height: auto !important;
        left: auto !important;
        overflow-y: visible !important;
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50 py-8 md:py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Tất cả sản phẩm</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="{{ route('storeFront') }}" class="hover:text-red-700">Trang chủ</a>
                <span class="mx-2">/</span>
                <span>Sản phẩm</span>
            </div>
        </div>

        <!-- Mobile Filter Button -->
        <div class="lg:hidden mb-6">
            <button id="filter-toggle" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 flex items-center justify-between text-gray-700 hover:bg-gray-50 transition-colors">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                    </svg>
                    Bộ lọc sản phẩm
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <!-- Filter Overlay for Mobile -->
        <div id="filter-overlay" class="filter-overlay lg:hidden"></div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:w-1/4">
                <div id="filter-sidebar" class="filter-sidebar lg:filter-sidebar-desktop bg-white rounded-lg shadow-sm p-6 lg:sticky lg:top-4">
                    <!-- Mobile Close Button -->
                    <div class="lg:hidden flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Bộ lọc sản phẩm</h2>
                        <button id="filter-close" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Desktop Title -->
                    <h2 class="hidden lg:block text-lg font-semibold text-gray-900 mb-6">Bộ lọc sản phẩm</h2>

                    <form id="filter-form" method="GET" action="{{ route('products.categories') }}">
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Nhập tên sản phẩm..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>

                        <!-- Categories -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Danh mục</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="category" value="all"
                                           {{ request('category', 'all') == 'all' ? 'checked' : '' }}
                                           class="text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-sm text-gray-700">Tất cả danh mục</span>
                                </label>
                                @foreach($categories as $category)
                                    <label class="flex items-center">
                                        <input type="radio" name="category" value="{{ $category->id }}"
                                               {{ request('category') == $category->id ? 'checked' : '' }}
                                               class="text-red-600 focus:ring-red-500">
                                        <span class="ml-2 text-sm text-gray-700">
                                            {{ $category->name }}
                                            <span class="text-gray-400">({{ $category->products_count }})</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range -->
                        @if($priceRange && $priceRange->min_price && $priceRange->max_price)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Khoảng giá</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                       placeholder="Từ {{ number_format($priceRange->min_price) }}"
                                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                       placeholder="Đến {{ number_format($priceRange->max_price) }}"
                                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                            </div>
                        </div>
                        @endif

                        <!-- Special Filters -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Đặc biệt</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_hot" value="1"
                                           {{ request('is_hot') ? 'checked' : '' }}
                                           class="text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-sm text-gray-700">Sản phẩm nổi bật</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_discount" value="1"
                                           {{ request('has_discount') ? 'checked' : '' }}
                                           class="text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-sm text-gray-700">Đang giảm giá</span>
                                </label>
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition-colors text-sm font-medium">
                                Áp dụng
                            </button>
                            <a href="{{ route('products.categories') }}" class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors text-sm font-medium text-center">
                                Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:w-3/4">
                @if(isset($products) && $products->count() > 0)
                    <!-- Results Header -->
                    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="text-sm text-gray-600">
                                Hiển thị {{ $products->firstItem() }}-{{ $products->lastItem() }} trong tổng số {{ $products->total() }} sản phẩm
                            </div>

                            <div class="flex items-center space-x-4">
                                <label for="sort" class="text-sm text-gray-600">Sắp xếp:</label>
                                <select id="sort" name="sort" onchange="updateSort(this.value)"
                                        class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Mặc định</option>
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp đến cao</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao đến thấp</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                        @foreach($products as $product)
                            <a href="{{ route('products.show', $product->slug) }}" class="group">
                                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden h-full flex flex-col transition-all duration-300 group-hover:shadow-lg group-hover:-translate-y-1">
                                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-100 relative">
                                        @php
                                            $image = $product->productImages->where('status', 'active')->sortBy('order')->first();
                                            $imageUrl = $image ? getProductImageUrlFromImage($image, $product->name) : null;
                                        @endphp
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}"
                                                alt="{{ getProductImageAlt($image, $product->name, $product->seo_title) }}"
                                                class="w-full h-full object-center object-cover transform transition-transform duration-500 group-hover:scale-110">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-50 to-red-100">
                                                <div class="text-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-300 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H9L3 7V9H21ZM12 19C10.9 19 10 18.1 10 17C10 15.9 10.9 15 12 15C13.1 15 14 15.9 14 17C14 18.1 13.1 19 12 19ZM12 13C9.8 13 8 14.8 8 17C8 19.2 9.8 21 12 21C14.2 21 16 19.2 16 17C16 14.8 14.2 13 12 13ZM5 11C3.9 11 3 11.9 3 13C3 14.1 3.9 15 5 15C6.1 15 7 14.1 7 13C7 11.9 6.1 11 5 11ZM19 11C17.9 11 17 11.9 17 13C17 14.1 17.9 15 19 15C20.1 15 21 14.1 21 13C21 11.9 20.1 11 19 11Z"/>
                                                    </svg>
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
                                        @if($product->hasDiscount())
                                            <div class="absolute top-2 right-2 bg-gradient-to-r from-red-500 to-red-600 text-white px-2 py-1 rounded-full text-xs font-bold shadow-lg">
                                                -{{ $product->getDiscountPercentage() }}%
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4 flex-grow flex flex-col">
                                        @if($product->category)
                                            <span class="text-xs text-red-500 font-medium uppercase tracking-wide mb-1">{{ $product->category->name }}</span>
                                        @endif
                                        <h3 class="text-sm md:text-base font-semibold text-gray-900 group-hover:text-red-700 transition-colors line-clamp-2 mb-3">{{ $product->name }}</h3>

                                        <div class="mt-auto">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    @if($product->hasDiscount())
                                                        <div class="flex flex-col">
                                                            <span class="text-red-600 font-bold text-sm md:text-base">{{ formatPrice($product->getCurrentPrice()) }}</span>
                                                            <span class="text-gray-400 line-through text-xs">{{ formatPrice($product->price) }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-red-600 font-bold text-sm md:text-base">{{ formatPrice($product->price) }}</span>
                                                    @endif
                                                </div>
                                                <span class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-red-50 to-red-100 px-3 py-1.5 text-xs font-medium text-red-700 group-hover:from-red-100 group-hover:to-red-200 transition-all">
                                                    Chi tiết
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m0 0V6a2 2 0 012-2h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V9a2 2 0 012 2v2M6 13h2m0 0v2a2 2 0 002 2h2m0 0h2a2 2 0 002-2v-2m0 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v2.586a1 1 0 00.293.707l2.414 2.414A1 1 0 0010.414 13H13z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Không tìm thấy sản phẩm</h3>
                        <p class="text-gray-500 mb-4">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                        <a href="{{ route('products.categories') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Xem tất cả sản phẩm
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateSort(sortValue) {
        const url = new URL(window.location);
        if (sortValue === 'default') {
            url.searchParams.delete('sort');
        } else {
            url.searchParams.set('sort', sortValue);
        }
        window.location.href = url.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Mobile filter toggle
        const filterToggle = document.getElementById('filter-toggle');
        const filterSidebar = document.getElementById('filter-sidebar');
        const filterOverlay = document.getElementById('filter-overlay');
        const filterClose = document.getElementById('filter-close');

        function openFilter() {
            filterSidebar.classList.add('active');
            filterOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeFilter() {
            filterSidebar.classList.remove('active');
            filterOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (filterToggle) {
            filterToggle.addEventListener('click', openFilter);
        }

        if (filterClose) {
            filterClose.addEventListener('click', closeFilter);
        }

        if (filterOverlay) {
            filterOverlay.addEventListener('click', closeFilter);
        }

        // Close filter on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFilter();
            }
        });

        // Auto submit form when radio buttons change
        const radioButtons = document.querySelectorAll('input[type="radio"][name="category"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        });

        // Auto submit form when checkboxes change
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Small delay to allow user to select multiple checkboxes
                setTimeout(() => {
                    document.getElementById('filter-form').submit();
                }, 300);
            });
        });
    });
</script>
@endpush
@endsection