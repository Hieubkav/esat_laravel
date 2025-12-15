@extends('layouts.shop')

@section('title', 'Liên hệ đặt hàng - Vũ Phúc Baking')

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
                <li><a href="{{ route('mshopkeeper.cart.show') }}" class="text-blue-600 hover:text-blue-800">Giỏ hàng</a></li>
                <li class="text-gray-500">/</li>
                <li class="text-gray-900 font-medium">Liên hệ đặt hàng</li>
            </ol>
        </div>
    </nav>

    <!-- Contact Content -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Liên hệ đặt hàng</h1>

                <div class="grid lg:grid-cols-2 gap-8">
                    <!-- Order Summary -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Đơn hàng của bạn</h2>
                        
                        <div class="space-y-4 mb-6">
                            @foreach($cart->items as $item)
                                <div class="flex items-center space-x-4 py-3 border-b border-gray-100">
                                    <div class="flex-shrink-0 w-16 h-16">
                                        @if($item->product->picture)
                                            <img src="{{ $item->product->picture }}" 
                                                 alt="{{ $item->product->name }}"
                                                 class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-birthday-cake text-red-300 text-lg"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 truncate">{{ $item->product->name }}</h3>
                                        <p class="text-sm text-gray-500">Mã: {{ $item->product->code }}</p>
                                        <p class="text-sm text-gray-600">Số lượng: {{ $item->quantity }}</p>
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="font-semibold text-red-600">{{ number_format($item->subtotal) }}đ</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900">Tổng cộng:</span>
                                <span class="text-2xl font-bold text-red-600">{{ number_format($cart->total_price) }}đ</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">{{ $cart->total_quantity }} sản phẩm</p>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Thông tin liên hệ</h2>
                        
                        @php
                            $settings = \App\Helpers\ViewDataHelper::getSettings();
                            $customer = Auth::guard('customer')->user();
                        @endphp

                        <!-- Customer Info -->
                        <div class="mb-8 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-blue-900 mb-3">Thông tin khách hàng</h3>
                            <div class="space-y-2 text-sm">
                                <div><strong>Tên:</strong> {{ $customer->name }}</div>
                                @if($customer->email)
                                    <div><strong>Email:</strong> {{ $customer->email }}</div>
                                @endif
                                @if($customer->phone)
                                    <div><strong>Điện thoại:</strong> {{ $customer->phone }}</div>
                                @endif
                                @if($customer->address)
                                    <div><strong>Địa chỉ:</strong> {{ $customer->address }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Store Contact Info -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-4">Liên hệ với chúng tôi</h3>
                                <div class="space-y-3">
                                    @if($settings->hotline)
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-full overflow-hidden shadow-sm">
                                                <img src="{{ asset('images/icon_phone.webp') }}"
                                                     alt="Phone" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Hotline</p>
                                                <a href="tel:{{ $settings->hotline }}" class="text-green-600 hover:text-green-700">
                                                    {{ $settings->hotline }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if($settings->email)
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-envelope text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Email</p>
                                                <a href="mailto:{{ $settings->email }}" class="text-blue-600 hover:text-blue-700">
                                                    {{ $settings->email }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if($settings->address)
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Địa chỉ</p>
                                                <p class="text-gray-600">{{ $settings->address }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($settings->working_hours)
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-clock text-yellow-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Giờ làm việc</p>
                                                <p class="text-gray-600">{{ $settings->working_hours }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Social Links -->
                            @if($settings->facebook_link || $settings->zalo_link || $settings->messenger_link)
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-4">Kết nối với chúng tôi</h3>
                                    <div class="grid grid-cols-1 gap-3">
                                        @if($settings->facebook_link)
                                            <a href="{{ $settings->facebook_link }}" target="_blank"
                                               class="flex items-center space-x-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors group">
                                                <div class="w-12 h-12 rounded-full overflow-hidden shadow-sm">
                                                    <img src="{{ asset('images/icon_facebook.webp') }}"
                                                         alt="Facebook" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">Facebook</p>
                                                    <p class="text-sm text-gray-600">Nhắn tin qua Facebook</p>
                                                </div>
                                                <i class="fas fa-external-link-alt text-gray-400 group-hover:text-blue-600"></i>
                                            </a>
                                        @endif

                                        @if($settings->zalo_link)
                                            <a href="{{ $settings->zalo_link }}" target="_blank"
                                               class="flex items-center space-x-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors group">
                                                <div class="w-12 h-12 rounded-full overflow-hidden shadow-sm">
                                                    <img src="{{ asset('images/icon_zalo.webp') }}"
                                                         alt="Zalo" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">Zalo</p>
                                                    <p class="text-sm text-gray-600">Chat qua Zalo</p>
                                                </div>
                                                <i class="fas fa-external-link-alt text-gray-400 group-hover:text-blue-600"></i>
                                            </a>
                                        @endif

                                        @if($settings->messenger_link)
                                            <a href="{{ $settings->messenger_link }}" target="_blank"
                                               class="flex items-center space-x-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors group">
                                                <div class="w-12 h-12 rounded-full overflow-hidden shadow-sm">
                                                    <img src="{{ asset('images/icon_messenger.webp') }}"
                                                         alt="Messenger" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">Messenger</p>
                                                    <p class="text-sm text-gray-600">Nhắn tin qua Messenger</p>
                                                </div>
                                                <i class="fas fa-external-link-alt text-gray-400 group-hover:text-blue-600"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="pt-6 border-t border-gray-200">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a href="{{ route('mshopkeeper.cart.show') }}" 
                                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold text-center transition-colors">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Quay lại giỏ hàng
                                    </a>
                                    
                                    @if($settings->hotline)
                                        <a href="tel:{{ $settings->hotline }}" 
                                           class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-semibold text-center transition-colors">
                                            <i class="fas fa-phone mr-2"></i>
                                            Gọi ngay
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-info-circle text-yellow-600 mt-1"></i>
                        <div>
                            <h3 class="font-semibold text-yellow-800 mb-2">Hướng dẫn đặt hàng</h3>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• Vui lòng liên hệ qua số hotline hoặc email để xác nhận đơn hàng</li>
                                <li>• Cung cấp thông tin chi tiết về sản phẩm và số lượng cần đặt</li>
                                <li>• Nhân viên sẽ tư vấn về thời gian giao hàng và phương thức thanh toán</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
