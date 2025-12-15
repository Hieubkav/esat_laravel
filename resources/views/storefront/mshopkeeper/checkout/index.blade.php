@extends('layouts.shop')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Đặt hàng</h1>
            <nav class="text-sm text-gray-600">
                <a href="{{ route('storeFront') }}" class="hover:text-red-600">Trang chủ</a>
                <span class="mx-2">/</span>
                <a href="{{ route('mshopkeeper.cart.show') }}" class="hover:text-red-600">Giỏ hàng</a>
                <span class="mx-2">/</span>
                <span class="text-red-600">Đặt hàng</span>
            </nav>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Form đặt hàng -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Thông tin giao hàng</h2>

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <ul class="text-sm text-red-600 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('mshopkeeper.order.place') }}" id="checkout-form">
                        @csrf

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tên người nhận <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="shipping_name" 
                                       name="shipping_name" 
                                       value="{{ old('shipping_name', $customer->name) }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                            </div>

                            <div>
                                <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Số điện thoại <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" 
                                       id="shipping_phone" 
                                       name="shipping_phone" 
                                       value="{{ old('shipping_phone', $customer->phone) }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" 
                                   id="shipping_email" 
                                   name="shipping_email" 
                                   value="{{ old('shipping_email', $customer->email) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>

                        <div class="mb-6">
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Địa chỉ giao hàng <span class="text-red-500">*</span>
                            </label>
                            <textarea id="shipping_address" 
                                      name="shipping_address" 
                                      rows="3" 
                                      required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                      placeholder="Nhập địa chỉ chi tiết...">{{ old('shipping_address', $customer->address) }}</textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Phương thức thanh toán <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="cod" checked class="text-red-600 focus:ring-red-500">
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-900">Thanh toán khi nhận hàng (COD)</div>
                                        <div class="text-sm text-gray-600">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="text-red-600 focus:ring-red-500">
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-900">Chuyển khoản ngân hàng</div>
                                        <div class="text-sm text-gray-600">Chuyển khoản trước khi giao hàng</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                                Ghi chú đơn hàng
                            </label>
                            <textarea id="note" 
                                      name="note" 
                                      rows="3" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                      placeholder="Ghi chú thêm về đơn hàng (tùy chọn)...">{{ old('note') }}</textarea>
                        </div>

                        <button type="submit" 
                                id="place-order-btn"
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-4 px-6 rounded-lg font-semibold text-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <span id="place-order-text">Đặt hàng</span>
                            <span id="place-order-loading" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Tóm tắt đơn hàng</h2>

                    <div class="space-y-4 mb-6">
                        @foreach($cart->items as $item)
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-16 h-16">
                                    @if($item->product->picture)
                                        <img src="{{ $item->product->picture }}" 
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-birthday-cake text-red-300 text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ number_format($item->price) }}đ x {{ $item->quantity }}</p>
                                </div>
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($item->subtotal) }}đ
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center text-lg font-semibold text-gray-900">
                            <span>Tổng cộng:</span>
                            <span class="text-red-600">{{ number_format($cart->total) }}đ</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Lưu ý:</p>
                                <ul class="space-y-1 text-xs">
                                    <li>• Đơn hàng sẽ được xác nhận trong vòng 24h</li>
                                    <li>• Thời gian giao hàng: 1-3 ngày làm việc</li>
                                    <li>• Miễn phí giao hàng trong nội thành</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('place-order-btn');
    const submitText = document.getElementById('place-order-text');
    const submitLoading = document.getElementById('place-order-loading');

    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
    });
});
</script>
@endsection
