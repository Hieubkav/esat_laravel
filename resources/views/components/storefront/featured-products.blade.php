@php
    $products = isset($featuredProducts) && !empty($featuredProducts) ? $featuredProducts : collect();
    $productsCount = $products->count();

    // Lấy dữ liệu từ WebDesign
    $featuredProductsData = webDesignData('featured-products');
    $isVisible = webDesignVisible('featured-products');
@endphp

@if($isVisible && $productsCount > 0)
<div class="container mx-auto px-4">
    <!-- Section Header -->
    <div class="text-center mb-10 md:mb-12">
        <h2 class="section-title mb-6">
            {{ $featuredProductsData?->title ?? 'Sản phẩm nổi bật' }}
        </h2>
        <p class="section-subtitle">
            {{ $featuredProductsData?->subtitle ?? 'Khám phá những sản phẩm chất lượng cao được khách hàng tin dùng và đánh giá cao nhất' }}
        </p>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @foreach($products as $product)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                <a href="{{ route('products.show', $product->slug) }}" class="block relative h-40 md:h-56 overflow-hidden">
                    @if(getProductImageUrl($product))
                        <img src="{{ getProductImageUrl($product) }}" alt="{{ $product->name }}" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 md:w-16 md:h-16 text-blue-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs text-blue-400 font-medium">{{ Str::limit($product->name, 15) }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Product badges -->
                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        @if($product->hasDiscount())
                            <span class="bg-red-600 text-white px-2 py-1 rounded-full text-xs font-bold">
                                -{{ $product->getDiscountPercentage() }}%
                            </span>
                        @endif
                        @if($product->is_hot)
                            <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                HOT
                            </span>
                        @endif
                    </div>
                </a>

                <div class="p-3 md:p-4">
                    @if($product->category)
                        <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full font-medium">{{ $product->category->name }}</span>
                    @endif
                    <h3 class="font-bold text-sm md:text-base text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors mt-2">
                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                    </h3>

                    <!-- Price and Action -->
                    <div class="flex justify-between items-center">
                        <div>
                            @if($product->price && $product->price > 0)
                                @if($product->hasDiscount())
                                    <div class="flex flex-col">
                                        <span class="text-blue-700 font-bold text-sm md:text-base">{{ formatPrice($product->getCurrentPrice()) }}</span>
                                        <span class="text-gray-400 line-through text-xs">{{ formatPrice($product->price) }}</span>
                                    </div>
                                @else
                                    <span class="text-blue-700 font-bold text-sm md:text-base">{{ formatPrice($product->price) }}</span>
                                @endif
                            @endif
                        </div>

                        <a href="{{ route('products.show', $product->slug) }}" class="inline-flex items-center text-xs text-blue-700 font-medium hover:text-blue-800 transition-colors">
                            <span>Chi tiết</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- View All Button -->
    <div class="text-center mt-8 md:mt-10">
        <a href="{{ route('products.categories') }}" class="group inline-flex items-center justify-center px-6 md:px-8 py-3 md:py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
            <span>Xem tất cả sản phẩm</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</div>
@endif
