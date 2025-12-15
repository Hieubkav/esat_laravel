@extends('layouts.shop')

@section('title', 'Tìm kiếm sản phẩm: ' . $query)
@section('meta_description', 'Kết quả tìm kiếm sản phẩm cho từ khóa: ' . $query)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header tìm kiếm -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 py-6">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Kết quả tìm kiếm sản phẩm
                </h1>
                @if(!empty($query))
                    <p class="text-gray-600 dark:text-gray-400">
                        Từ khóa: <span class="font-semibold text-red-600 dark:text-red-400">"{{ $query }}"</span>
                        @if($total > 0)
                            - Tìm thấy {{ number_format($total) }} kết quả
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            @if(empty($query))
                <!-- Trạng thái chưa tìm kiếm -->
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tìm kiếm sản phẩm</h3>
                        <p class="text-gray-500 dark:text-gray-400">Nhập từ khóa để tìm kiếm sản phẩm bạn cần</p>
                    </div>
                </div>
            @elseif($products->count() > 0)
                <!-- Hiển thị kết quả -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden group">
                            <div class="aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
                                @php
                                    $image = $product->picture ?? ($product->gallery_images[0] ?? null);
                                @endphp
                                @if($image)
                                    <img src="{{ $image }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy"
                                         onerror="this.src='{{ asset('images/no-image.svg') }}'; this.onerror=null;">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                    <a href="{{ route('mshopkeeper.inventory.show', $product->code) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>

                                @if($product->category_name)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                        {{ $product->category_name }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between mb-2">
                                    @if($product->price_hidden || $product->selling_price <= 0)
                                        <p class="text-lg font-bold text-red-600 dark:text-red-400">
                                            Liên hệ
                                        </p>
                                    @elseif($product->selling_price)
                                        <p class="text-lg font-bold text-red-600 dark:text-red-400">
                                            {{ number_format($product->selling_price, 0, ',', '.') }}đ
                                        </p>
                                    @endif

                                    <span class="text-xs px-2 py-1 rounded-full {{ $product->total_on_hand > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->total_on_hand > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                    </span>
                                </div>

                                @if($product->code)
                                    <p class="text-xs text-gray-400 font-mono">
                                        Mã: {{ $product->code }}
                                    </p>
                                @endif
                                
                                @if($product->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($product->description), 80) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Không có kết quả -->
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div class="mx-auto w-16 h-16 mb-4 flex items-center justify-center bg-red-50 dark:bg-red-900/20 rounded-full">
                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 21h6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Không tìm thấy sản phẩm</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            Không có sản phẩm nào phù hợp với từ khóa "<span class="font-semibold">{{ $query }}</span>"
                        </p>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <p>Gợi ý:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Kiểm tra lại chính tả</li>
                                <li>Thử từ khóa khác</li>
                                <li>Sử dụng từ khóa ngắn gọn hơn</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
