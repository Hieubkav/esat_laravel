@extends('layouts.shop')

@section('title', 'Giỏ hàng - Vũ Phúc Baking')

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
                <li class="text-gray-900 font-medium">Giỏ hàng</li>
            </ol>
        </div>
    </nav>

    <!-- Cart Content -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Giỏ hàng của bạn</h1>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                @if(!$validation['valid'])
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                        <h4 class="font-semibold mb-2">Cảnh báo tồn kho:</h4>
                        <ul class="list-disc list-inside">
                            @foreach($validation['errors'] as $error)
                                <li>{{ $error['product_name'] }}: Yêu cầu {{ $error['requested'] }}, còn lại {{ $error['available'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($cart && $cart->items->count() > 0)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <!-- Cart Items -->
                        <div class="divide-y divide-gray-200">
                            @foreach($cart->items as $item)
                                <div class="p-6 flex items-center space-x-4" id="cart-item-{{ $item->id }}">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-20 h-20">
                                        @if($item->product->picture)
                                            <img src="{{ $item->product->picture }}" 
                                                 alt="{{ $item->product->name }}"
                                                 class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-birthday-cake text-red-300 text-2xl"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            <a href="{{ route('mshopkeeper.inventory.show', $item->product->code) }}"
                                               class="hover:text-red-600 transition-colors duration-200 inline-flex items-center group">
                                                <span>{{ $item->product->name }}</span>
                                                <i class="fas fa-external-link-alt ml-2 text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-500">Mã: {{ $item->product->code }}</p>
                                        <p class="text-lg font-bold text-red-600 mt-1">
                                            {{ number_format($item->product->selling_price) }}đ
                                        </p>
                                        @if($item->product->total_on_hand < $item->quantity)
                                            <p class="text-sm text-red-500 mt-1">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Chỉ còn {{ $item->product->total_on_hand }} sản phẩm
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-3">
                                        <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            <i class="fas fa-minus text-sm"></i>
                                        </button>
                                        
                                        <span class="w-12 text-center font-semibold" id="quantity-{{ $item->id }}">
                                            {{ $item->quantity }}
                                        </span>
                                        
                                        <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                                                {{ $item->product->total_on_hand <= $item->quantity ? 'disabled' : '' }}>
                                            <i class="fas fa-plus text-sm"></i>
                                        </button>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900" id="subtotal-{{ $item->id }}">
                                            {{ number_format($item->subtotal) }}đ
                                        </p>
                                    </div>

                                    <!-- Remove Button -->
                                    <button onclick="removeItem({{ $item->id }})"
                                            class="text-red-500 hover:text-red-700 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <!-- Cart Summary -->
                        <div class="bg-gray-50 px-6 py-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-600">Tổng số lượng: {{ $cart->total_quantity }} sản phẩm</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-red-600" id="total-price">
                                        {{ number_format($cart->total_price) }}đ
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="px-6 py-4 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <button onclick="clearCart()"
                                        class="bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    Xóa tất cả
                                </button>

                                <a href="{{ route('mshopkeeper.checkout') }}"
                                   class="bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-semibold text-center transition-colors">
                                    <i class="fas fa-shopping-bag mr-2"></i>
                                    Đặt hàng ngay
                                </a>


                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty Cart -->
                    <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                        <div class="mb-6">
                            <i class="fas fa-shopping-cart text-6xl text-gray-300"></i>
                        </div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Giỏ hàng trống</h2>
                        <p class="text-gray-600 mb-8">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                        <a href="{{ route('mshopkeeper.inventory.index') }}" 
                           class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-semibold transition-colors">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Tiếp tục mua sắm
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Update quantity function
async function updateQuantity(itemId, newQuantity) {
    try {
        const response = await fetch(`/gio-hang/cap-nhat/${itemId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQuantity })
        });

        const result = await response.json();
        
        if (result.success) {
            if (newQuantity <= 0) {
                document.getElementById(`cart-item-${itemId}`).remove();
            } else {
                document.getElementById(`quantity-${itemId}`).textContent = newQuantity;
            }
            location.reload(); // Reload to update totals
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại');
    }
}

// Remove item function
async function removeItem(itemId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
    
    try {
        const response = await fetch(`/gio-hang/xoa/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        
        if (result.success) {
            document.getElementById(`cart-item-${itemId}`).remove();
            location.reload(); // Reload to update totals
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại');
    }
}

// Clear cart function
async function clearCart() {
    if (!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) return;
    
    try {
        const response = await fetch('/gio-hang/xoa-tat-ca', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại');
    }
}
</script>
@endpush
@endsection
