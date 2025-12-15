<div>
    <!-- Loading Overlay -->
    <div wire:loading.flex class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-red-600"></div>
            <span class="text-gray-700">Đang xử lý...</span>
        </div>
    </div>

    <section class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Giỏ hàng của bạn</h1>
                <nav class="text-sm text-gray-500">
                    <a href="{{ route('storeFront') }}" class="hover:text-red-600">Trang chủ</a> /
                    <span>Giỏ hàng</span>
                </nav>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                @guest('mshopkeeper_customer')
                    <!-- Not Authenticated -->
                    <div class="lg:col-span-3">
                        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                            <div class="mb-6">
                                <i class="fas fa-user-lock text-6xl text-gray-300"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Vui lòng đăng nhập</h2>
                            <p class="text-gray-600 mb-8">Bạn cần đăng nhập để xem giỏ hàng và thực hiện mua sắm</p>
                            <div class="space-y-4">
                                <button onclick="openAuthModal('login', '{{ url()->current() }}')"
                                        class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-semibold transition-colors">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Đăng nhập ngay
                                </button>
                                <div class="text-sm text-gray-500">
                                    hoặc <a href="{{ route('mshopkeeper.inventory.index') }}" class="text-red-600 hover:text-red-700 font-medium">tiếp tục mua sắm</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @if($cart && $cart->items->count() > 0)
                        <!-- Cart Items -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <!-- Header -->
                                <div class="bg-gray-50 px-6 py-4 border-b">
                                    <div class="flex justify-between items-center">
                                        <h2 class="text-xl font-semibold text-gray-900">
                                            Sản phẩm ({{ $cart->items->count() }})
                                        </h2>
                                        <button wire:click="clearCart" 
                                                wire:confirm="Bạn có chắc muốn xóa toàn bộ giỏ hàng?"
                                                class="text-red-500 hover:text-red-700 text-sm font-medium">
                                            Xóa tất cả
                                        </button>
                                    </div>
                                </div>

                                <!-- Items List -->
                                <div class="divide-y divide-gray-200">
                                    @foreach($cart->items as $item)
                                        <div class="p-6 hover:bg-gray-50 transition-colors" 
                                             wire:key="cart-item-{{ $item->id }}">
                                            <div class="flex items-center space-x-4">
                                                <!-- Product Image -->
                                                <div class="flex-shrink-0">
                                                    @if(getMShopKeeperImageUrl($item->product))
                                                        <img src="{{ getMShopKeeperImageUrl($item->product) }}"
                                                             alt="{{ $item->product->name }}"
                                                             class="w-20 h-20 object-cover rounded-lg">
                                                    @else
                                                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-image text-gray-400"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Product Info -->
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="text-lg font-semibold truncate">
                                                        <a href="{{ route('mshopkeeper.inventory.show', $item->product->code) }}"
                                                           class="text-gray-900 hover:text-red-600 transition-colors duration-200 inline-flex items-center group">
                                                            <span>{{ $item->product->name }}</span>
                                                            <i class="fas fa-external-link-alt ml-2 text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                                        </a>
                                                    </h3>
                                                    <p class="text-sm text-gray-500">
                                                        Mã: {{ $item->product->code }}
                                                    </p>
                                                    <p class="text-lg font-bold text-red-600 mt-1">
                                                        {{ number_format($item->product->selling_price) }}đ
                                                    </p>
                                                </div>

                                                <!-- Quantity Controls -->
                                                <div class="flex items-center space-x-3">
                                                    <button wire:click="decreaseQuantity({{ $item->id }}, {{ $item->quantity }})"
                                                            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors"
                                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-minus text-sm"></i>
                                                    </button>
                                                    
                                                    <span class="w-12 text-center font-semibold">
                                                        {{ $item->quantity }}
                                                    </span>
                                                    
                                                    <button wire:click="increaseQuantity({{ $item->id }}, {{ $item->quantity }})"
                                                            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors"
                                                            {{ $item->product->total_on_hand <= $item->quantity ? 'disabled' : '' }}>
                                                        <i class="fas fa-plus text-sm"></i>
                                                    </button>
                                                </div>

                                                <!-- Subtotal -->
                                                <div class="text-right">
                                                    <p class="text-lg font-bold text-gray-900">
                                                        {{ number_format($item->subtotal) }}đ
                                                    </p>
                                                </div>

                                                <!-- Remove Button -->
                                                <button wire:click="removeItem({{ $item->id }})"
                                                        wire:confirm="Bạn có chắc muốn xóa sản phẩm này?"
                                                        class="text-red-500 hover:text-red-700 p-2 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Stock Warning -->
                                            @if($item->product->total_on_hand < $item->quantity)
                                                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                    <p class="text-sm text-yellow-800">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        Chỉ còn {{ $item->product->total_on_hand }} sản phẩm trong kho
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Cart Summary -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">Tóm tắt đơn hàng</h3>
                                
                                <div class="space-y-3 mb-6">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Số lượng:</span>
                                        <span class="font-semibold">{{ $cart->total_quantity }} sản phẩm</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold text-red-600 pt-3 border-t">
                                        <span>Tổng cộng:</span>
                                        <span>{{ number_format($cart->total_price) }}đ</span>
                                    </div>
                                </div>

                                <!-- Validation Messages -->
                                @if(!$validation['valid'])
                                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-sm text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            {{ $validation['message'] }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="space-y-3">
                                    <a href="{{ route('mshopkeeper.inventory.index') }}" 
                                       class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 py-3 px-6 rounded-lg font-semibold text-center transition-colors block">
                                        Tiếp tục mua sắm
                                    </a>

                                    <button wire:click="openQuickOrderModal"
                                            wire:loading.attr="disabled"
                                            class="w-full bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white py-3 px-6 rounded-lg font-semibold text-center transition-colors">
                                        <span wire:loading.remove wire:target="openQuickOrderModal">
                                            <i class="fas fa-shopping-cart mr-2"></i>
                                            Đặt hàng ngay
                                        </span>
                                        <span wire:loading wire:target="openQuickOrderModal">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>
                                            Đang xử lý...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Empty Cart -->
                        <div class="lg:col-span-3">
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
                        </div>
                    @endif
                @endguest
            </div>
        </div>
    </section>

    <!-- Quick Order Modal -->
    @if($this->showQuickOrderModal)

        <div class="fixed inset-0 z-50 overflow-y-auto"
             x-data="{ show: @entangle('showQuickOrderModal') }"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50"
                 wire:click="closeQuickOrderModal"></div>

            <!-- Modal Content -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-auto max-h-[90vh] overflow-y-auto"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <!-- Header -->
                    <div class="sticky top-0 bg-white border-b px-6 py-4 rounded-t-lg">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-shopping-cart mr-2 text-red-600"></i>
                                Đặt hàng nhanh
                            </h3>
                            <button wire:click="closeQuickOrderModal"
                                    class="text-gray-400 hover:text-gray-500 text-2xl leading-none">
                                ×
                            </button>
                        </div>
                    </div>

                    <!-- Customer Info (Read-only) -->
                    @auth('mshopkeeper_customer')
                        @php $customer = auth('mshopkeeper_customer')->user(); @endphp
                        <div class="px-6 py-4 bg-blue-50 border-b">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Thông tin khách hàng</h4>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">{{ $customer->name }}</p>
                                <p>{{ $customer->tel }}</p>
                                @if($customer->email)
                                    <p>{{ $customer->email }}</p>
                                @endif
                            </div>
                        </div>
                    @endauth

                    <!-- Order Form -->
                    <div class="p-6 space-y-4">
                        <!-- Địa chỉ giao hàng -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Địa chỉ giao hàng <span class="text-red-500">*</span>
                            </label>
                            @error('shippingAddress')
                                <textarea wire:model.lazy="shippingAddress"
                                          placeholder="Nhập địa chỉ giao hàng chi tiết..."
                                          class="w-full p-3 border border-red-500 rounded-lg text-base focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                          rows="3"
                                          required></textarea>
                            @else
                                <textarea wire:model.lazy="shippingAddress"
                                          placeholder="Nhập địa chỉ giao hàng chi tiết..."
                                          class="w-full p-3 border border-gray-300 rounded-lg text-base focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                          rows="3"
                                          required></textarea>
                            @enderror
                            @error('shippingAddress')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phương thức thanh toán <span class="text-red-500">*</span>
                            </label>
                            @error('paymentMethod')
                                <select wire:model.lazy="paymentMethod"
                                        class="w-full p-3 border border-red-500 rounded-lg text-base focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        required>
                            @else
                                <select wire:model.lazy="paymentMethod"
                                        class="w-full p-3 border border-gray-300 rounded-lg text-base focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        required>
                            @enderror
                                <option value="cod">💰 Thanh toán khi nhận hàng (COD)</option>
                                <option value="bank_transfer">🏦 Chuyển khoản ngân hàng</option>
                            </select>
                            @error('paymentMethod')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ghi chú -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ghi chú đơn hàng
                            </label>
                            @error('orderNote')
                                <textarea wire:model.lazy="orderNote"
                                          placeholder="Ghi chú thêm cho đơn hàng (tùy chọn)..."
                                          class="w-full p-3 border border-red-500 rounded-lg text-base focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                          rows="2"></textarea>
                            @else
                                <textarea wire:model.lazy="orderNote"
                                          placeholder="Ghi chú thêm cho đơn hàng (tùy chọn)..."
                                          class="w-full p-3 border border-gray-300 rounded-lg text-base focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                          rows="2"></textarea>
                            @enderror
                            @error('orderNote')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Order Summary -->
                    @if($cart && $cart->items->count() > 0)
                        <div class="px-6 py-4 bg-gray-50 border-t border-b">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Tóm tắt đơn hàng</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Số lượng:</span>
                                    <span>{{ $cart->total_quantity }} sản phẩm</span>
                                </div>
                                <div class="flex justify-between font-semibold text-red-600">
                                    <span>Tổng tiền:</span>
                                    <span>{{ number_format($cart->total_price) }}đ</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Footer Actions -->
                    <div class="sticky bottom-0 bg-white px-6 py-4 border-t rounded-b-lg">
                        <div class="flex space-x-3">
                            <button type="button"
                                    wire:click="closeQuickOrderModal"
                                    class="flex-1 py-3 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                                Hủy
                            </button>
                            <button type="button"
                                    wire:click="submitQuickOrder"
                                    wire:loading.attr="disabled"
                                    class="flex-1 py-3 px-4 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 disabled:bg-red-400 transition-colors">
                                <span wire:loading.remove wire:target="submitQuickOrder">
                                    Đặt hàng
                                </span>
                                <span wire:loading wire:target="submitQuickOrder">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Đang xử lý...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
