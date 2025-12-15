@extends('layouts.shop')

@section('title', 'Sản Phẩm Nổi Bật - Kho Hàng')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <nav class="bg-white border-b border-gray-200 py-4">
        <div class="container mx-auto px-4">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('storeFront') }}" class="text-blue-600 hover:text-blue-800">Trang chủ</a></li>
                <li class="text-gray-500">/</li>
                <li><a href="{{ route('mshopkeeper.inventory.index') }}" class="text-blue-600 hover:text-blue-800">Kho hàng</a></li>
                <li class="text-gray-500">/</li>
                <li class="text-gray-900 font-medium">Sản phẩm nổi bật</li>
            </ol>
        </div>
    </nav>

    <!-- Header -->
    <section class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    <i class="fas fa-star mr-3"></i>
                    Sản Phẩm Nổi Bật
                </h1>
                <p class="text-xl opacity-90 mb-8">
                    Những sản phẩm có nhiều tồn kho và được quan tâm nhất từ hệ thống MShopKeeper
                </p>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="text-2xl font-bold">{{ $products->count() }}</div>
                        <div class="text-sm opacity-90">Sản phẩm nổi bật</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="text-2xl font-bold">{{ $products->where('total_on_hand', '>', 0)->count() }}</div>
                        <div class="text-sm opacity-90">Còn hàng</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="text-2xl font-bold">{{ $products->sum('total_on_hand') }}</div>
                        <div class="text-sm opacity-90">Tổng tồn kho</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($products as $index => $product)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group relative">
                            <!-- Featured Badge -->
                            <div class="absolute top-4 left-4 z-10">
                                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-xs font-bold flex items-center">
                                    <i class="fas fa-star mr-1"></i>
                                    #{{ $index + 1 }}
                                </div>
                            </div>

                            <!-- Product Image -->
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                @if($product->picture)
                                    <img src="{{ asset('storage/' . $product->picture) }}" 
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                         onerror="this.src='{{ asset('images/no-image.svg') }}'; this.onerror=null;">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-5xl text-gray-400"></i>
                                    </div>
                                @endif
                                
                                <!-- Stock Badge -->
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full shadow-lg
                                        {{ $product->total_on_hand > 100 ? 'bg-green-500 text-white' : 
                                           ($product->total_on_hand > 10 ? 'bg-blue-500 text-white' : 
                                           ($product->total_on_hand > 0 ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white')) }}">
                                        {{ $product->stock_status }}
                                    </span>
                                </div>

                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300"></div>
                            </div>

                            <!-- Product Info -->
                            <div class="p-6">
                                <div class="mb-3">
                                    <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                        {{ $product->item_type_text }}
                                    </span>
                                </div>
                                
                                <h3 class="font-bold text-gray-900 mb-3 text-lg line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    <a href="{{ route('mshopkeeper.inventory.show', $product->code) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                
                                <div class="text-sm text-gray-600 mb-3">
                                    <span class="font-medium">Mã:</span> {{ $product->code }}
                                </div>
                                
                                <!-- Price and Stock -->
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <div class="text-xl font-bold text-red-600">
                                            {{ number_format($product->selling_price) }}đ
                                        </div>
                                        @if($product->cost_price > 0 && $product->cost_price != $product->selling_price)
                                            <div class="text-sm text-gray-500 line-through">
                                                {{ number_format($product->cost_price) }}đ
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="text-right">
                                        <div class="text-sm text-gray-600">Tồn kho</div>
                                        <div class="text-lg font-bold {{ $product->total_on_hand > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $product->total_on_hand }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                @if($product->description)
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                        {{ $product->description }}
                                    </p>
                                @endif

                                <!-- Action Button -->
                                <a href="{{ route('mshopkeeper.inventory.show', $product->code) }}" 
                                   class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-center py-3 px-4 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-eye mr-2"></i>
                                    Xem Chi Tiết
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Call to Action -->
                <div class="text-center mt-16">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-8 text-white">
                        <h2 class="text-2xl font-bold mb-4">Tìm hiểu thêm về sản phẩm?</h2>
                        <p class="text-lg opacity-90 mb-6">Khám phá toàn bộ kho hàng với hàng nghìn sản phẩm đa dạng</p>
                        <div class="space-x-4">
                            <a href="{{ route('mshopkeeper.inventory.index') }}" 
                               class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                                <i class="fas fa-warehouse mr-2"></i>
                                Xem Toàn Bộ Kho Hàng
                            </a>
                            <a href="{{ route('mshopkeeper.inventory.index') }}"
                               class="inline-block border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                                <i class="fas fa-search mr-2"></i>
                                Tìm Kiếm Sản Phẩm
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <i class="fas fa-star text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Chưa có sản phẩm nổi bật</h3>
                    <p class="text-gray-600 mb-6">Hiện tại chưa có sản phẩm nào được đánh dấu là nổi bật</p>
                    <a href="{{ route('mshopkeeper.inventory.index') }}" 
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Xem Toàn Bộ Sản Phẩm
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom gradient animation */
@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}
</style>
@endpush
@endsection
