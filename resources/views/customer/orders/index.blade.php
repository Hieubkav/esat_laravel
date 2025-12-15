@extends('layouts.shop')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 px-3">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Đơn hàng của tôi</h1>
            <p class="text-gray-600">Quản lý và theo dõi đơn hàng của bạn</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 shadow-sm border">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $orderStats['total'] }}</div>
                    <div class="text-sm text-gray-600">Tổng đơn</div>
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm border">
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $orderStats['pending'] }}</div>
                    <div class="text-sm text-gray-600">Chờ xử lý</div>
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm border">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $orderStats['processing'] }}</div>
                    <div class="text-sm text-gray-600">Đang xử lý</div>
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm border">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $orderStats['completed'] }}</div>
                    <div class="text-sm text-gray-600">Hoàn thành</div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow-sm border mb-6 p-4">
            <form method="GET" class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-[200px]">
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Lọc
                </button>
                @if(request()->hasAny(['status']))
                    <a href="{{ route('customer.orders.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Xóa bộ lọc
                    </a>
                @endif
            </form>
        </div>

        <!-- Orders List -->
        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                    @php
                        $statusConfig = App\Http\Controllers\CustomerOrderController::getStatusConfig($order->status);
                        $paymentConfig = App\Http\Controllers\CustomerOrderController::getPaymentStatusConfig($order->payment_status ?? 'pending');
                    @endphp
                    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                        <!-- Order Header -->
                        <div class="p-4 border-b bg-gray-50">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                <div class="flex flex-col md:flex-row md:items-center gap-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Đơn hàng #{{ $order->order_number }}</h3>
                                        <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig['color'] }}">
                                            <i class="{{ $statusConfig['icon'] }} mr-1"></i>
                                            {{ $statusConfig['label'] }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentConfig['color'] }}">
                                            {{ $paymentConfig['label'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <div class="font-semibold text-lg text-red-600">{{ number_format($order->total) }}đ</div>
                                        <div class="text-sm text-gray-600">{{ $order->items->count() }} sản phẩm</div>
                                    </div>
                                    <a href="{{ route('customer.orders.show', $order->order_number) }}" 
                                       class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items Preview -->
                        <div class="p-4">
                            <div class="space-y-3">
                                @foreach($order->items->take(3) as $item)
                                    <div class="flex items-center gap-3">
                                        <!-- Product Image -->
                                        @if($item->mshopkeeperProduct && $item->mshopkeeperProduct->picture)
                                            <a href="{{ route('mshopkeeper.inventory.show', $item->mshopkeeperProduct->code) }}" class="flex-shrink-0">
                                                <img src="{{ $item->mshopkeeperProduct->picture }}"
                                                     alt="{{ $item->product_name }}"
                                                     class="w-12 h-12 object-cover rounded-lg hover:opacity-80 transition-opacity">
                                            </a>
                                        @elseif($item->product && $item->product->productImages->first())
                                            <img src="{{ asset('storage/' . $item->product->productImages->first()->image_link) }}"
                                                 alt="{{ $item->product_name }}"
                                                 class="w-12 h-12 object-cover rounded-lg">
                                        @else
                                            <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-birthday-cake text-red-300 text-lg"></i>
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            @if($item->mshopkeeperProduct)
                                                <a href="{{ route('mshopkeeper.inventory.show', $item->mshopkeeperProduct->code) }}"
                                                   class="font-medium text-gray-900 text-sm hover:text-red-600 transition-colors">
                                                    {{ $item->product_name }}
                                                </a>
                                            @else
                                                <h4 class="font-medium text-gray-900 text-sm">{{ $item->product_name }}</h4>
                                            @endif
                                            <p class="text-sm text-gray-600">
                                                {{ number_format($item->price) }}đ x {{ $item->quantity }}
                                                @if($item->product_code)
                                                    <span class="text-xs text-gray-400 ml-1">({{ $item->product_code }})</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($item->subtotal) }}đ
                                        </div>
                                    </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <div class="text-center pt-2">
                                        <span class="text-sm text-gray-500">và {{ $order->items->count() - 3 }} sản phẩm khác...</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="mt-6">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có đơn hàng nào</h3>
                <p class="text-gray-600 mb-6">Bạn chưa có đơn hàng nào. Hãy khám phá các sản phẩm của chúng tôi!</p>
                <a href="{{ route('ecomerce.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Mua sắm ngay
                </a>
            </div>
        @endif

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="{{ route('storeFront') }}" class="text-sm text-gray-500 hover:text-gray-700">← Về trang chủ</a>
        </div>
    </div>
</div>
@endsection
