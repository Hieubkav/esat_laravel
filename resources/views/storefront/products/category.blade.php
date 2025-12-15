@extends('layouts.shop')

@section('title', $category->name . ' - Vũ Phúc Baking')
@section('description', ($category->seo_description ?? 'Khám phá các sản phẩm ' . strtolower($category->name) . ' chất lượng cao tại Vũ Phúc Baking. Nguyên liệu, dụng cụ và thiết bị chuyên nghiệp cho ngành bánh và pha chế.'))

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
</style>
@endpush

@section('content')
<div class="bg-gray-50 py-8 md:py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="{{ route('storeFront') }}" class="hover:text-red-700">Trang chủ</a>
                <span class="mx-2">/</span>
                <a href="{{ route('products.categories') }}" class="hover:text-red-700">Sản phẩm</a>
                <span class="mx-2">/</span>
                <span>{{ $category->name }}</span>
            </div>
        </div>

        @if($category->description)
            <div class="mb-8 max-w-3xl">
                <div class="prose prose-red">
                    {!! $category->description !!}
                </div>
            </div>
        @endif

        @if($products->count() > 0)
                <!-- Filter và Sort -->
                <div class="filter-card rounded-3xl shadow-xl p-8 mb-12">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                        <div class="flex items-center text-gray-600 font-open-sans">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Hiển thị <span class="font-semibold text-gray-900">{{ $products->firstItem() }}-{{ $products->lastItem() }}</span> trong tổng số <span class="font-semibold text-gray-900">{{ $products->total() }}</span> sản phẩm
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                </svg>
                                <label for="sort" class="text-sm text-gray-600 font-open-sans font-medium mr-3">Sắp xếp:</label>
                            </div>
                            <select id="sort" name="sort" onchange="updateSort(this.value)"
                                    class="border-2 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white/50 backdrop-blur-sm transition-all duration-300 font-open-sans">
                                <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Mặc định</option>
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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-16">
                    @foreach($products as $product)
                        <a href="{{ route('products.show', $product->slug) }}" class="group">
                            <div class="product-card bg-white rounded-3xl overflow-hidden shadow-xl h-full flex flex-col">
                                <!-- Product Image -->
                                <div class="aspect-square w-full overflow-hidden relative">
                                    @php
                                        $image = $product->productImages->where('status', 'active')->sortBy('order')->first();
                                        $imageUrl = $image ? getProductImageUrlFromImage($image, $product->name) : null;
                                    @endphp
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}"
                                            alt="{{ getProductImageAlt($image, $product->name, $product->seo_title) }}"
                                            class="w-full h-full object-center object-cover transition-transform duration-700 group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-50 to-red-100 relative overflow-hidden">
                                            <!-- Background pattern -->
                                            <div class="absolute inset-0 opacity-10">
                                                <div class="absolute top-4 left-4 w-3 h-3 bg-red-200 rounded-full"></div>
                                                <div class="absolute top-8 right-6 w-2 h-2 bg-red-200 rounded-full"></div>
                                                <div class="absolute bottom-6 left-8 w-2 h-2 bg-red-200 rounded-full"></div>
                                                <div class="absolute bottom-4 right-4 w-3 h-3 bg-red-200 rounded-full"></div>
                                            </div>

                                            <!-- Main icon -->
                                            <div class="relative z-10 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-300 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H9L3 7V9H21ZM12 19C10.9 19 10 18.1 10 17C10 15.9 10.9 15 12 15C13.1 15 14 15.9 14 17C14 18.1 13.1 19 12 19ZM12 13C9.8 13 8 14.8 8 17C8 19.2 9.8 21 12 21C14.2 21 16 19.2 16 17C16 14.8 14.2 13 12 13ZM5 11C3.9 11 3 11.9 3 13C3 14.1 3.9 15 5 15C6.1 15 7 14.1 7 13C7 11.9 6.1 11 5 11ZM19 11C17.9 11 17 11.9 17 13C17 14.1 17.9 15 19 15C20.1 15 21 14.1 21 13C21 11.9 20.1 11 19 11Z"/>
                                                </svg>
                                                <p class="text-xs text-red-400 font-medium">{{ Str::limit($product->name, 15) }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Product badges -->
                                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                                        @if($product->hasDiscount())
                                            <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                -{{ $product->getDiscountPercentage() }}%
                                            </span>
                                        @endif
                                        @if($product->is_hot)
                                            <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                HOT
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="p-6 flex-grow flex flex-col">
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-red-700 transition-colors mb-3 font-montserrat line-clamp-2">
                                        {{ $product->name }}
                                    </h3>

                                    <!-- Product Meta -->
                                    <div class="flex items-center gap-2 mb-4">
                                        @if($product->brand)
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $product->brand }}</span>
                                        @endif
                                        @if($product->category)
                                            <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-full">{{ $product->category->name }}</span>
                                        @endif
                                    </div>

                                    <!-- Price Section -->
                                    <div class="mt-auto">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                @if($product->hasDiscount())
                                                    <p class="text-sm text-gray-400 line-through font-open-sans">{{ formatPrice($product->price) }}</p>
                                                    <p class="text-xl font-bold text-red-700 font-montserrat">{{ formatPrice($product->getCurrentPrice()) }}</p>
                                                @else
                                                    <p class="text-xl font-bold text-red-700 font-montserrat">{{ formatPrice($product->price) }}</p>
                                                @endif
                                                @if($product->unit)
                                                    <p class="text-xs text-gray-500 font-open-sans">{{ $product->unit }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-2xl p-3 border border-red-200">
                                            <div class="flex items-center justify-center text-red-700 font-medium text-sm">
                                                <span class="font-open-sans">Xem chi tiết</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-gray-500">Không có sản phẩm nào trong danh mục này</p>
                <a href="{{ route('products.categories') }}" class="mt-4 inline-flex items-center text-red-700 hover:text-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    <span>Quay lại danh mục sản phẩm</span>
                </a>
            </div>
        @endif
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
</script>
@endpush
@endsection