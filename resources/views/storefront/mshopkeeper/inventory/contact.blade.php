@extends('layouts.shop')

@section('title', $seoData['title'] . ' - Vũ Phúc Baking')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <nav class="bg-white border-b border-gray-200 py-4">
        <div class="container mx-auto px-4">
            <ol class="flex items-center space-x-2 text-sm">
                @foreach($seoData['breadcrumbs'] as $breadcrumb)
                    @if($loop->last)
                        <li class="text-gray-900 font-medium">{{ $breadcrumb['name'] }}</li>
                    @else
                        <li><a href="{{ $breadcrumb['url'] }}" class="text-blue-600 hover:text-blue-800">{{ $breadcrumb['name'] }}</a></li>
                        <li class="text-gray-500">/</li>
                    @endif
                @endforeach
            </ol>
        </div>
    </nav>

    <!-- Contact Content -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Liên hệ đặt hàng</h1>

                <div class="grid lg:grid-cols-2 gap-8">
                    <!-- Product Info -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Thông tin sản phẩm</h2>
                        
                        <div class="flex items-start space-x-4 mb-6">
                            <div class="flex-shrink-0 w-24 h-24">
                                @if($product->picture)
                                    <img src="{{ $product->picture }}" 
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover rounded-lg">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-birthday-cake text-red-300 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                <div class="space-y-1 text-sm text-gray-600">
                                    <div><strong>Mã sản phẩm:</strong> {{ $product->code }}</div>
                                    @if($product->barcode)
                                        <div><strong>Mã vạch:</strong> {{ $product->barcode }}</div>
                                    @endif
                                    <div><strong>Loại:</strong> {{ $product->item_type_text }}</div>
                                    @if($product->category_name)
                                        <div><strong>Danh mục:</strong> {{ $product->category_name }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-lg font-semibold text-gray-900">Giá bán:</span>
                                <span class="text-2xl font-bold text-red-600">{{ number_format($product->selling_price) }}đ</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Tồn kho:</span>
                                <span class="text-sm font-medium {{ $product->total_on_hand > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->total_on_hand }} {{ $product->unit_name ?? 'sản phẩm' }}
                                </span>
                            </div>
                        </div>

                        @if($product->description)
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <h4 class="font-semibold text-gray-900 mb-2">Mô tả sản phẩm</h4>
                                <div class="text-sm text-gray-700 leading-relaxed">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Additional Product Info -->
                        @if($product->color || $product->size || $product->material)
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <h4 class="font-semibold text-gray-900 mb-3">Thông tin chi tiết</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    @if($product->color)
                                        <div>
                                            <span class="text-sm text-gray-600">Màu sắc:</span>
                                            <div class="font-medium">{{ $product->color }}</div>
                                        </div>
                                    @endif
                                    @if($product->size)
                                        <div>
                                            <span class="text-sm text-gray-600">Kích thước:</span>
                                            <div class="font-medium">{{ $product->size }}</div>
                                        </div>
                                    @endif
                                    @if($product->material)
                                        <div class="col-span-2">
                                            <span class="text-sm text-gray-600">Chất liệu:</span>
                                            <div class="font-medium">{{ $product->material }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Thông tin liên hệ</h2>
                        
                        @php
                            $settings = \App\Helpers\ViewDataHelper::getSettings();
                        @endphp

                        <!-- Store Contact Info -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-4">Liên hệ với chúng tôi</h3>
                                <div class="space-y-4">
                                    @if($settings->hotline)
                                        <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                            <div class="w-12 h-12 rounded-full overflow-hidden shadow-sm">
                                                <img src="{{ asset('images/icon_phone.webp') }}"
                                                     alt="Phone" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">Hotline</p>
                                                <a href="tel:{{ $settings->hotline }}" class="text-green-600 hover:text-green-700 font-semibold text-lg">
                                                    {{ $settings->hotline }}
                                                </a>
                                            </div>
                                            <a href="tel:{{ $settings->hotline }}"
                                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                                                Gọi ngay
                                            </a>
                                        </div>
                                    @endif

                                    @if($settings->email)
                                        <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-envelope text-blue-600 text-lg"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">Email</p>
                                                <a href="mailto:{{ $settings->email }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                                                    {{ $settings->email }}
                                                </a>
                                            </div>
                                            <a href="mailto:{{ $settings->email }}" 
                                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                                                Gửi mail
                                            </a>
                                        </div>
                                    @endif

                                    @if($settings->address)
                                        <div class="flex items-start space-x-3 p-3 bg-red-50 rounded-lg">
                                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-map-marker-alt text-red-600 text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Địa chỉ</p>
                                                <p class="text-gray-600">{{ $settings->address }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($settings->working_hours)
                                        <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg">
                                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-clock text-yellow-600 text-lg"></i>
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
                                    <a href="{{ route('mshopkeeper.inventory.show', $product->code) }}" 
                                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold text-center transition-colors">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Quay lại sản phẩm
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
                                <li>• Vui lòng liên hệ qua số hotline hoặc email để đặt hàng sản phẩm <strong>{{ $product->name }}</strong></li>
                                <li>• Cung cấp mã sản phẩm: <strong>{{ $product->code }}</strong> để được tư vấn nhanh chóng</li>
                                <li>• Nhân viên sẽ tư vấn về số lượng có sẵn, thời gian giao hàng và phương thức thanh toán</li>
                                @if($product->total_on_hand <= 5 && $product->total_on_hand > 0)
                                    <li class="text-red-600 font-semibold">• <i class="fas fa-exclamation-triangle mr-1"></i>Sản phẩm sắp hết hàng, vui lòng liên hệ sớm để đặt hàng!</li>
                                @elseif($product->total_on_hand <= 0)
                                    <li class="text-red-600 font-semibold">• <i class="fas fa-times-circle mr-1"></i>Sản phẩm hiện đang hết hàng, vui lòng liên hệ để biết thời gian nhập hàng!</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
