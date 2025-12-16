@php
    $title = $data['title'] ?? 'Sản phẩm nổi bật';
    $subtitle = $data['subtitle'] ?? '';
    $viewAllLink = $data['view_all_link'] ?? '/san-pham';
    // $products được truyền từ Controller (đã eager load productImages)
    $productsCount = $products->count();
@endphp

@if($productsCount > 0)
<div class="container mx-auto px-4">
    <!-- Section Header -->
    <div class="text-center mb-10 md:mb-12">
        <h2 class="section-title mb-6">{{ $title }}</h2>
        @if($subtitle)
        <p class="section-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    <!-- MOBILE VERSION - Hiển thị chỉ trên thiết bị di động (dưới md) -->
    <div class="md:hidden">
        <div class="grid grid-cols-2 gap-4">
            @foreach($products as $product)
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 group">
                    <a href="{{ route('products.show', $product->slug) }}" class="block relative h-40 overflow-hidden">
                        @if($product->thumbnail)
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-50 to-red-100 relative overflow-hidden">
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-2 left-2 w-2 h-2 bg-red-200 rounded-full"></div>
                                    <div class="absolute top-4 right-3 w-1 h-1 bg-red-200 rounded-full"></div>
                                    <div class="absolute bottom-3 left-4 w-1 h-1 bg-red-200 rounded-full"></div>
                                    <div class="absolute bottom-2 right-2 w-2 h-2 bg-red-200 rounded-full"></div>
                                </div>
                                <div class="relative z-10 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-red-300 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-xs text-red-400 font-medium">{{ Str::limit($product->name, 12) }}</p>
                                </div>
                            </div>
                        @endif
                    </a>

                    <div class="p-4">
                        @if($product->category)
                            <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-full font-medium">{{ $product->category->name }}</span>
                        @endif
                        <h3 class="font-bold text-sm text-gray-900 mb-3 line-clamp-2 group-hover:text-red-600 transition-colors mt-2">
                            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                        </h3>

                        <div class="flex justify-between items-center">
                            <div>
                                @if($product->price)
                                    <span class="text-red-700 font-bold text-sm">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="text-gray-500 text-sm">Liên hệ</span>
                                @endif
                            </div>

                            <a href="{{ route('products.show', $product->slug) }}" class="inline-flex items-center text-xs text-red-700 font-medium hover:text-red-800 transition-colors">
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

        <!-- Nút xem thêm cho mobile -->
        @if($viewAllLink)
        <div class="text-center mt-6">
            <a href="{{ $viewAllLink }}" class="group inline-flex items-center justify-center px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-2xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <span>Xem tất cả sản phẩm</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
        @endif
    </div>

    <!-- DESKTOP VERSION - Hiển thị chỉ từ md trở lên -->
    <div class="hidden md:block">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($products as $product)
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden transition-all hover:-translate-y-2 hover:shadow-2xl duration-300 group">
                    <a href="{{ route('products.show', $product->slug) }}" class="block relative h-64 overflow-hidden">
                        @if($product->thumbnail)
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="object-cover w-full h-full transition-transform hover:scale-105 duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-50 to-red-100 relative overflow-hidden">
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-4 left-4 w-3 h-3 bg-red-200 rounded-full"></div>
                                    <div class="absolute top-8 right-6 w-2 h-2 bg-red-200 rounded-full"></div>
                                    <div class="absolute bottom-6 left-8 w-2 h-2 bg-red-200 rounded-full"></div>
                                    <div class="absolute bottom-4 right-4 w-3 h-3 bg-red-200 rounded-full"></div>
                                </div>
                                <div class="relative z-10 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-red-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-sm text-red-400 font-medium">{{ Str::limit($product->name, 20) }}</p>
                                </div>
                            </div>
                        @endif
                    </a>

                    <div class="p-6">
                        @if($product->category)
                            <span class="text-sm text-red-600 bg-red-50 px-3 py-1 rounded-full font-medium">{{ $product->category->name }}</span>
                        @endif
                        <h3 class="font-bold text-lg text-gray-900 mb-4 line-clamp-2 group-hover:text-red-600 transition-colors mt-3">
                            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                        </h3>

                        <div class="flex justify-between items-center">
                            <div>
                                @if($product->price)
                                    <span class="text-red-700 font-bold text-xl">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="text-gray-500">Liên hệ</span>
                                @endif
                            </div>

                            <a href="{{ route('products.show', $product->slug) }}" class="inline-flex items-center text-sm text-red-700 font-medium hover:text-red-800 transition-colors">
                                <span>Chi tiết</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Nút xem thêm desktop -->
        @if($viewAllLink)
        <div class="text-center mt-12">
            <a href="{{ $viewAllLink }}" class="group inline-flex items-center px-10 py-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-2xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <span>Xem tất cả sản phẩm</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-3 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
        @endif
    </div>
</div>
@else
<div class="container mx-auto px-4">
    <p class="text-center text-gray-500">Chưa có sản phẩm</p>
</div>
@endif
