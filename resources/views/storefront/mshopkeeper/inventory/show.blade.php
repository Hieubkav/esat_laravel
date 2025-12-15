@extends('layouts.shop')

@section('title', $product->name . ' - Chi tiết sản phẩm')

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

    <!-- Product Detail -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 p-6">
                    <!-- Product Image Gallery -->
                    <div class="space-y-4 lg:col-span-5">
                        @php
                            $galleryImages = $product->gallery_images;
                            $mainImage = !empty($galleryImages) ? $galleryImages[0] : $product->picture;
                            $hasMultipleImages = count($galleryImages) > 1;
                        @endphp

                        <!-- Main Image -->
                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden relative">
                            @if($mainImage)
                                <img id="main-image"
                                     src="{{ $mainImage }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.src='{{ asset('images/no-image.svg') }}'; this.onerror=null;">

                                <!-- Image Counter -->
                                @if($hasMultipleImages)
                                    <div class="absolute top-4 right-4 bg-black bg-opacity-70 text-white px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-images mr-1"></i>
                                        {{ count($galleryImages) }} ảnh
                                    </div>
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-image text-6xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-500 text-sm">Không có ảnh</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Zoom Button -->
                        <div class="mt-3">
                            <button onclick="openImageGallery(0)"
                                    class="w-full bg-red-50 hover:bg-red-100 text-red-700 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center border border-red-200 hover:border-red-300">
                                <i class="fas fa-expand mr-2"></i>
                                Phóng to
                            </button>
                        </div>



                        <!-- Thumbnail Gallery -->
                        @if($hasMultipleImages)
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($galleryImages as $index => $image)
                                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer border-2 transition-all duration-200 {{ $index === 0 ? 'border-red-500' : 'border-gray-200 hover:border-red-300' }}"
                                         onclick="changeMainImage('{{ $image }}', {{ $index }}, this)">
                                        <img src="{{ $image }}"
                                             alt="{{ $product->name }} - Ảnh {{ $index + 1 }}"
                                             class="w-full h-full object-cover transition-transform duration-200 hover:scale-105"
                                             onerror="this.src='{{ asset('images/no-image.svg') }}'; this.onerror=null;">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-5 lg:col-span-7">
                        <!-- Category and Product Type -->
                        <div class="flex items-center space-x-2">
                            @if($product->category_name)
                                <span class="inline-block px-3 py-1 text-sm font-medium text-red-600 bg-red-100 rounded-full border border-red-200">
                                    {{ $product->category_name }}
                                </span>
                            @endif
                            <span class="inline-block px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-full border border-gray-200">
                                {{ $product->item_type_text }}
                            </span>
                        </div>

                        <!-- Product Name -->
                        <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>

                        <!-- Product Code -->
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span><strong>Mã sản phẩm:</strong> {{ $product->code }}</span>
                            @if($product->unit_name)
                                <span><strong>Đơn vị tính:</strong> {{ $product->unit_name }}</span>
                            @endif
                        </div>

                        <!-- Price -->
                        <div class="space-y-2">
                            <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg p-3">
                                @if($product->price_hidden || $product->selling_price <= 0)
                                    <div class="text-2xl font-bold text-red-600">
                                        Liên hệ
                                    </div>
                                @else
                                    <div class="text-2xl font-bold text-red-600">
                                        {{ number_format($product->selling_price) }}đ
                                    </div>
                                @endif
                                @if($product->unit_name)
                                    <div class="text-red-500 text-sm mt-1">Đơn vị: {{ $product->unit_name }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Stock Info -->
                        <div class="text-gray-600">
                            <strong>Tồn kho:</strong> {{ $product->total_on_hand }} {{ $product->unit_name ?? 'sản phẩm' }}
                        </div>

                        <!-- Product Details -->
                        @if($product->description)
                            <div class="space-y-2">
                                <h3 class="text-lg font-semibold text-gray-900">Mô tả sản phẩm</h3>
                                <div class="text-gray-700 leading-relaxed">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Additional Info -->
                        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
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
                                <div>
                                    <span class="text-sm text-gray-600">Chất liệu:</span>
                                    <div class="font-medium">{{ $product->material }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- Quantity Selector -->
                        @if($product->total_on_hand > 0)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Số lượng</label>
                                <div class="flex items-center space-x-3">
                                    <button type="button" onclick="decreaseQuantity()"
                                            class="w-10 h-10 rounded-full bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center border border-red-200">
                                        <i class="fas fa-minus text-sm"></i>
                                    </button>

                                    <input type="number" id="quantity" value="1" min="1" max="{{ $product->total_on_hand }}"
                                           class="w-20 text-center border-2 border-red-200 focus:border-red-500 rounded-lg py-2 font-semibold focus:outline-none">

                                    <button type="button" onclick="increaseQuantity()"
                                            class="w-10 h-10 rounded-full bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center border border-red-200">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>

                                    <span class="text-sm text-red-500 font-medium">Tồn kho: {{ $product->total_on_hand }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Compact Product Details (moved up) -->
                        @if($product->description || $product->color || $product->size || $product->material || $product->barcode || $product->unit_name)
                            <div class="hidden lg:block">
                                <h3 class="text-base font-semibold text-gray-900 mb-2">Thông tin chi tiết</h3>
                                <div class="grid grid-cols-2 gap-3 p-3 bg-gray-50 rounded-lg">
                                    @if($product->barcode)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Mã vạch</span>
                                            <span class="text-sm font-medium text-gray-900 font-mono">{{ $product->barcode }}</span>
                                        </div>
                                    @endif
                                    @if($product->unit_name)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Đơn vị tính</span>
                                            <span class="text-sm font-medium text-gray-900">{{ $product->unit_name }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-600">Loại sản phẩm</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $product->item_type_text }}</span>
                                    </div>
                                    @if($product->color)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Màu sắc</span>
                                            <span class="text-sm font-medium text-gray-900">{{ $product->color }}</span>
                                        </div>
                                    @endif
                                    @if($product->size)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Kích thước</span>
                                            <span class="text-sm font-medium text-gray-900">{{ $product->size }}</span>
                                        </div>
                                    @endif
                                    @if($product->material)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Chất liệu</span>
                                            <span class="text-sm font-medium text-gray-900">{{ $product->material }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            @if($product->total_on_hand > 0)
                                @auth('mshopkeeper_customer')
                                    <button onclick="addToCart()" id="add-to-cart-btn"
                                            class="w-full bg-red-600 hover:bg-red-700 text-white py-2.5 px-5 rounded-lg font-semibold text-base transition-all duration-200 shadow-md hover:shadow-lg">
                                        <span id="add-to-cart-text">Thêm vào Giỏ hàng</span>
                                    </button>
                                @else
                                    <button onclick="openAuthModal('login', '{{ url()->current() }}')"
                                            class="w-full bg-red-600 hover:bg-red-700 text-white py-2.5 px-5 rounded-lg font-semibold text-base transition-colors">
                                        Đăng nhập để mua hàng
                                    </button>
                                @endauth

                                <a href="{{ route('mshopkeeper.product.contact', $product->code) }}"
                                   class="w-full bg-white hover:bg-red-50 text-red-600 border-2 border-red-600 hover:border-red-700 py-2.5 px-5 rounded-lg font-semibold text-base transition-colors block text-center shadow-md hover:shadow-lg">
                                    <i class="fas fa-phone mr-2"></i>
                                    Liên hệ ngay
                                </a>
                            @else
                                <button class="w-full bg-gray-400 text-white py-2.5 px-5 rounded-lg font-semibold text-base cursor-not-allowed" disabled>
                                    <i class="fas fa-times mr-2"></i>
                                    Hết hàng
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Details & Description (hidden on lg for compact above-the-fold) -->
    @if($product->description || $product->color || $product->size || $product->material || $product->barcode)
        <section class="py-8 lg:hidden">
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Thông tin chi tiết</h2>

                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Product Description -->
                        @if($product->description)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mô tả sản phẩm</h3>
                                <div class="prose prose-gray max-w-none">
                                    <p class="text-gray-700 leading-relaxed">
                                        {!! nl2br(e($product->description)) !!}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Product Specifications -->
                        @if($product->color || $product->size || $product->material || $product->barcode)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông số kỹ thuật</h3>
                                <div class="space-y-3">
                                    @if($product->barcode)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-gray-600">Mã vạch:</span>
                                            <span class="font-medium text-gray-900 font-mono">{{ $product->barcode }}</span>
                                        </div>
                                    @endif

                                    @if($product->color)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-gray-600">Màu sắc:</span>
                                            <span class="font-medium text-gray-900">{{ $product->color }}</span>
                                        </div>
                                    @endif

                                    @if($product->size)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-gray-600">Kích thước:</span>
                                            <span class="font-medium text-gray-900">{{ $product->size }}</span>
                                        </div>
                                    @endif

                                    @if($product->material)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-gray-600">Chất liệu:</span>
                                            <span class="font-medium text-gray-900">{{ $product->material }}</span>
                                        </div>
                                    @endif

                                    @if($product->unit_name)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-gray-600">Đơn vị tính:</span>
                                            <span class="font-medium text-gray-900">{{ $product->unit_name }}</span>
                                        </div>
                                    @endif

                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Loại sản phẩm:</span>
                                        <span class="font-medium text-gray-900">{{ $product->item_type_text }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Product Information Cards -->
                    <div class="mt-8 pt-6 border-t border-red-100 space-y-6">
                        <!-- Mô tả sản phẩm Card -->
                        @if($product->description && trim($product->description) !== '')
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                                <div class="mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">📝 Thành phần & Mô tả</h3>
                                </div>
                                <div class="text-gray-700 leading-relaxed">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Thông số kỹ thuật Card -->
                        @php
                            $hasSpecs = false;
                            $specs = [
                                'unit_name' => $product->unit_name,
                                'item_type_text' => $product->item_type_text,
                                'color' => $product->color,
                                'size' => $product->size,
                                'material' => $product->material
                            ];
                            foreach($specs as $spec) {
                                if($spec && trim($spec) !== '') {
                                    $hasSpecs = true;
                                    break;
                                }
                            }
                        @endphp
                        @if($hasSpecs)
                            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                                <div class="mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">🔧 Thông số kỹ thuật</h3>
                                </div>
                                <div class="space-y-1">
                                    @if($product->unit_name && trim($product->unit_name) !== '')
                                        <div class="flex items-center justify-between py-2 border-b border-green-100">
                                            <span class="text-gray-600">Đơn vị tính:</span>
                                            <span class="font-medium text-gray-900">{{ $product->unit_name }}</span>
                                        </div>
                                    @endif

                                    @if($product->item_type_text && trim($product->item_type_text) !== '')
                                        <div class="flex items-center justify-between py-2 border-b border-green-100">
                                            <span class="text-gray-600">Loại sản phẩm:</span>
                                            <span class="font-medium text-gray-900">{{ $product->item_type_text }}</span>
                                        </div>
                                    @endif

                                    @if($product->color && trim($product->color) !== '')
                                        <div class="flex items-center justify-between py-2 border-b border-green-100">
                                            <span class="text-gray-600">Màu sắc:</span>
                                            <span class="font-medium text-gray-900">{{ $product->color }}</span>
                                        </div>
                                    @endif

                                    @if($product->size && trim($product->size) !== '')
                                        <div class="flex items-center justify-between py-2 border-b border-green-100">
                                            <span class="text-gray-600">Kích thước:</span>
                                            <span class="font-medium text-gray-900">{{ $product->size }}</span>
                                        </div>
                                    @endif

                                    @if($product->material && trim($product->material) !== '')
                                        <div class="flex items-center justify-between py-2 border-b border-green-100">
                                            <span class="text-gray-600">Chất liệu:</span>
                                            <span class="font-medium text-gray-900">{{ $product->material }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <section class="py-8">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">
                    @if($product->category_name)
                        Sản phẩm cùng danh mục: {{ $product->category_name }}
                    @else
                        Sản phẩm liên quan
                    @endif
                </h2>

                @if($relatedProducts->count() > 4)
                    <!-- Carousel for many products -->
                    <div class="relative">
                        <div class="swiper related-products-swiper">
                            <div class="swiper-wrapper">
                @else
                    <!-- Grid for few products -->
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                @endif
                    @foreach($relatedProducts as $relatedProduct)
                        @if($relatedProducts->count() > 4)
                            <div class="swiper-slide">
                        @endif
                        <article class="group">
                            <a href="{{ route('mshopkeeper.inventory.show', $relatedProduct->code) }}" class="block">
                                <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                                    <!-- Product Image -->
                                    <div class="aspect-square overflow-hidden relative">
                                        @if($relatedProduct->picture)
                                            <img src="{{ $relatedProduct->picture }}"
                                                 alt="{{ $relatedProduct->name }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <!-- Custom placeholder giống /kho-hang -->
                                            <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 flex flex-col items-center justify-center relative overflow-hidden">
                                                <div class="text-center">
                                                    <i class="fas fa-birthday-cake text-4xl text-red-300 mb-2"></i>
                                                    <p class="text-xs text-red-400 font-medium">Vũ Phúc Baking</p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Badges -->
                                        <div class="absolute top-2 left-2 flex flex-col gap-1">
                                            @if($relatedProduct->total_on_hand > 0)
                                                <span class="bg-red-50 text-red-700 text-xs px-2 py-0.5 rounded-full font-medium shadow-sm border border-red-100">Còn hàng</span>
                                            @endif
                                        </div>

                                    </div>

                                    <!-- Product Info -->
                                    <div class="p-4">
                                        <span class="text-xs text-red-500 font-medium uppercase tracking-wide mb-1 block">
                                            {{ match($relatedProduct->item_type) {
                                                1 => 'Hàng Hoá',
                                                2 => 'Combo',
                                                4 => 'Dịch Vụ',
                                                default => 'Khác'
                                            } }}
                                        </span>
                                        <h3 class="text-sm md:text-base font-semibold text-gray-900 group-hover:text-red-700 transition-colors line-clamp-2 mb-3 font-montserrat">
                                            {{ $relatedProduct->name }}
                                        </h3>

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-red-600 font-bold text-sm md:text-base">{{ number_format($relatedProduct->selling_price, 0, ',', '.') }}đ</span>
                                            </div>
                                            <span class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-red-50 to-red-100 px-3 py-1.5 text-xs font-medium text-red-700 group-hover:from-red-100 group-hover:to-red-200 transition-all">
                                                Chi tiết
                                                <i class="fas fa-arrow-right ml-1"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </article>
                        @if($relatedProducts->count() > 4)
                            </div>
                        @endif
                    @endforeach

                @if($relatedProducts->count() > 4)
                            </div>
                        </div>
                        <!-- Navigation buttons -->
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                @else
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>

<!-- Image Gallery Popup -->
<div id="image-gallery-popup" class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center" role="dialog" aria-modal="true" aria-label="Xem ảnh lớn">
    <div class="relative w-full h-full flex items-center justify-center p-4">

        <!-- Previous Button -->
        <button id="prev-btn" onclick="previousImage()" aria-label="Ảnh trước"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 bg-black/60 hover:bg-black/70 text-white rounded-full p-3 transition-all duration-200"
                style="display: none;">
            <i class="fas fa-chevron-left text-xl"></i>
        </button>

        <!-- Next Button -->
        <button id="next-btn" onclick="nextImage()" aria-label="Ảnh tiếp theo"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 bg-black/60 hover:bg-black/70 text-white rounded-full p-3 transition-all duration-200"
                style="display: none;">
            <i class="fas fa-chevron-right text-xl"></i>
        </button>

        <!-- Main Image -->
        <div class="relative w-full h-full flex items-center justify-center">
            <div class="relative inline-block">
                <img id="popup-image"
                     src=""
                     alt="{{ $product->name }}"
                     class="max-w-[96vw] max-h-[90vh] object-contain rounded-lg shadow-2xl">

                <!-- Close Button (anchored to image corner) -->
                <button onclick="closeImageGallery()" aria-label="Đóng"
                        class="absolute -top-3 -right-3 md:-top-4 md:-right-4 z-10 bg-white/90 hover:bg-white text-gray-700 rounded-full p-2 md:p-2.5 shadow-lg ring-1 ring-black/10 transition duration-200">
                    <i class="fas fa-times text-base md:text-lg"></i>
                </button>

                <!-- Image Counter -->
                <div id="image-counter" class="absolute bottom-2 left-1/2 transform -translate-x-1/2 bg-black/70 text-white px-3 py-1.5 rounded-full text-xs md:text-sm font-medium" style="display: none;">
                    <span id="current-image">1</span> / <span id="total-images">1</span>
                </div>
            </div>
        </div>

        <!-- Thumbnail Strip -->
        <div id="popup-thumbnails-container" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 max-w-md" style="display: none;">
            <div class="flex space-x-2 overflow-x-auto pb-2" id="popup-thumbnails">
                <!-- Thumbnails will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.related-products-swiper .swiper-button-next,
.related-products-swiper .swiper-button-prev {
    color: #dc2626;
    background: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.related-products-swiper .swiper-button-next:after,
.related-products-swiper .swiper-button-prev:after {
    font-size: 16px;
}

.related-products-swiper .swiper-pagination-bullet {
    background: #dc2626;
}

/* Hide dot pagination when navigation arrows are present */
/* Pagination dots appear as sibling after the swiper container; hide them */
.related-products-swiper .swiper-pagination,
.related-products-swiper + .swiper-pagination,
.related-products-swiper ~ .swiper-pagination {
    display: none !important;
}

/* Image Gallery Popup Styles */
/* No backdrop for transparent modal overlay */
#image-gallery-popup {
    /* full-screen overlay with subtle backdrop for focus */
}

#image-gallery-popup.hidden {
    display: none !important;
}

#image-gallery-popup:not(.hidden) {
    display: flex !important;
}

#popup-image {
    max-width: 96vw;
    max-height: 90vh;
    object-fit: contain;
}

/* Ensure comfortable viewing size on medium+ screens */
@media (min-width: 768px) {
    #popup-image {
        min-width: 640px;
        min-height: 640px;
    }
}

/* Smooth transitions for gallery */
.gallery-transition {
    transition: all 0.3s ease;
}

/* Thumbnail hover effects */
.popup-thumb:hover {
    transform: scale(1.05);
}

/* Cart animation */
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0,-30px,0);
    }
    70% {
        transform: translate3d(0,-15px,0);
    }
    90% {
        transform: translate3d(0,-4px,0);
    }
}

.animate-bounce {
    animation: bounce 1s ease-in-out;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Gallery Images Data
const galleryImages = @json($galleryImages ?? []);
let currentImageIndex = 0;

// Image Gallery Functions
function changeMainImage(imageSrc, index, element) {
    // Update main image
    document.getElementById('main-image').src = imageSrc;
    currentImageIndex = index;

    // Update thumbnail borders
    const thumbnails = document.querySelectorAll('.aspect-square.cursor-pointer.border-2');
    thumbnails.forEach((thumb, i) => {
        if (i === index) {
            thumb.classList.remove('border-gray-200', 'hover:border-red-300');
            thumb.classList.add('border-red-500');
        } else {
            thumb.classList.remove('border-red-500');
            thumb.classList.add('border-gray-200', 'hover:border-red-300');
        }
    });
}

function openImageGallery(index = 0) {
    console.log('Opening gallery with index:', index);
    console.log('Gallery images:', galleryImages);

    // Fallback: nếu không có galleryImages, lấy từ main image
    let imagesToShow = galleryImages;
    if (!imagesToShow || imagesToShow.length === 0) {
        const mainImageSrc = document.getElementById('main-image')?.src;
        if (mainImageSrc) {
            imagesToShow = [mainImageSrc];
            console.log('Using main image as fallback:', mainImageSrc);
        } else {
            console.log('No images available');
            return;
        }
    }

    currentImageIndex = index;
    const popup = document.getElementById('image-gallery-popup');
    const popupImage = document.getElementById('popup-image');

    if (!popup || !popupImage) {
        console.log('Popup elements not found');
        return;
    }

    // Show popup
    popup.classList.remove('hidden');
    popup.style.display = 'flex';

    // Set image
    popupImage.src = imagesToShow[currentImageIndex];
    console.log('Setting popup image to:', imagesToShow[currentImageIndex]);

    // Show/hide navigation buttons based on image count
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const imageCounter = document.getElementById('image-counter');
    const thumbnailsContainer = document.getElementById('popup-thumbnails-container');

    if (imagesToShow.length > 1) {
        if (prevBtn) prevBtn.style.display = 'block';
        if (nextBtn) nextBtn.style.display = 'block';
        if (imageCounter) {
            imageCounter.style.display = 'block';
            document.getElementById('total-images').textContent = imagesToShow.length;
        }
        if (thumbnailsContainer) thumbnailsContainer.style.display = 'block';
    } else {
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'none';
        if (imageCounter) imageCounter.style.display = 'none';
        if (thumbnailsContainer) thumbnailsContainer.style.display = 'none';
    }

    // Update counter and thumbnails
    updateImageCounter();
    updatePopupThumbnails();

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeImageGallery() {
    const popup = document.getElementById('image-gallery-popup');
    if (popup) {
        popup.classList.add('hidden');
        popup.style.display = 'none';
    }

    // Restore body scroll
    document.body.style.overflow = '';
}

function previousImage() {
    if (galleryImages.length <= 1) return;

    currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : galleryImages.length - 1;
    updatePopupImage();
}

function nextImage() {
    if (galleryImages.length <= 1) return;

    currentImageIndex = currentImageIndex < galleryImages.length - 1 ? currentImageIndex + 1 : 0;
    updatePopupImage();
}

function goToImage(index) {
    currentImageIndex = index;
    updatePopupImage();
}

function updatePopupImage() {
    const popupImage = document.getElementById('popup-image');
    popupImage.src = galleryImages[currentImageIndex];

    updateImageCounter();
    updatePopupThumbnails();
}

function updateImageCounter() {
    const currentElement = document.getElementById('current-image');
    if (currentElement) {
        currentElement.textContent = currentImageIndex + 1;
    }
}

function updatePopupThumbnails() {
    const thumbnails = document.querySelectorAll('.popup-thumb');
    thumbnails.forEach((thumb, index) => {
        if (index === currentImageIndex) {
            thumb.classList.remove('border-transparent', 'hover:border-gray-300');
            thumb.classList.add('border-white');
        } else {
            thumb.classList.remove('border-white');
            thumb.classList.add('border-transparent', 'hover:border-gray-300');
        }
    });
}

// Keyboard navigation for gallery
document.addEventListener('keydown', function(e) {
    const popup = document.getElementById('image-gallery-popup');
    if (!popup.classList.contains('hidden')) {
        switch(e.key) {
            case 'Escape':
                closeImageGallery();
                break;
            case 'ArrowLeft':
                previousImage();
                break;
            case 'ArrowRight':
                nextImage();
                break;
        }
    }
});

// Close popup when clicking outside image
document.addEventListener('click', function(e) {
    const popup = document.getElementById('image-gallery-popup');
    if (e.target === popup) {
        closeImageGallery();
    }
});

// Quantity controls
function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const maxValue = parseInt(quantityInput.max);

    if (currentValue < maxValue) {
        quantityInput.value = currentValue + 1;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);

    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
}

// Add to cart function
async function addToCart() {
    const quantityInput = document.getElementById('quantity');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const addToCartText = document.getElementById('add-to-cart-text');

    if (!quantityInput || !addToCartBtn) return;

    const quantity = parseInt(quantityInput.value);
    const productId = {{ $product->id }};

    // Disable button and show loading
    addToCartBtn.disabled = true;
    addToCartBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
    addToCartBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
    addToCartText.innerHTML = 'Đang thêm...';

    try {
        const response = await fetch('/gio-hang/them', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });

        const result = await response.json();

        if (result.success) {
            // Show success state
            addToCartBtn.classList.remove('bg-gray-400');
            addToCartBtn.classList.add('bg-green-600');
            addToCartText.innerHTML = 'Đã Thêm vào Giỏ hàng!';

            // Dispatch event to update cart icon
            window.Livewire.dispatch('cartUpdated');

            // Show notification
            showNotification(result.message, 'success');

            // Add cart bounce animation
            const cartIcons = document.querySelectorAll('[aria-label="Giỏ hàng"]');
            cartIcons.forEach(icon => {
                icon.classList.add('animate-bounce');
                setTimeout(() => icon.classList.remove('animate-bounce'), 1000);
            });

            // Show "Go to Cart" option after success
            setTimeout(() => {
                addToCartBtn.classList.remove('bg-green-600', 'cursor-not-allowed');
                addToCartBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                addToCartText.innerHTML = 'Xem Giỏ hàng';
                addToCartBtn.disabled = false;

                // Change button action to go to cart
                addToCartBtn.onclick = function() {
                    window.location.href = '/gio-hang';
                };
            }, 2000);

            // Reset to original state after 6 seconds
            setTimeout(() => {
                addToCartBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                addToCartBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                addToCartText.innerHTML = 'Thêm vào Giỏ hàng';
                addToCartBtn.onclick = addToCart;
            }, 6000);

        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error:', error);

        // Show error state
        addToCartBtn.classList.remove('bg-gray-400');
        addToCartBtn.classList.add('bg-red-500');
        addToCartText.innerHTML = 'Thêm thất bại!';
        showNotification(error.message || 'Có lỗi xảy ra, vui lòng thử lại', 'error');

        // Reset button after 3 seconds
        setTimeout(() => {
            addToCartBtn.disabled = false;
            addToCartBtn.classList.remove('bg-red-500', 'cursor-not-allowed');
            addToCartBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            addToCartText.innerHTML = 'Thêm vào Giỏ hàng';
        }, 3000);
    }
}

// Show notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;

    if (type === 'success') {
        notification.classList.add('bg-green-500', 'text-white');
        notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
    } else if (type === 'error') {
        notification.classList.add('bg-red-500', 'text-white');
        notification.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${message}`;
    } else {
        notification.classList.add('bg-blue-500', 'text-white');
        notification.innerHTML = `<i class="fas fa-info-circle mr-2"></i>${message}`;
    }

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Debug function
function debugGallery() {
    console.log('Gallery Images:', galleryImages);
    console.log('Popup element:', document.getElementById('image-gallery-popup'));
    console.log('Popup image element:', document.getElementById('popup-image'));
}

// Swiper initialization
document.addEventListener('DOMContentLoaded', function() {
    // Debug gallery on page load
    debugGallery();

    const relatedProductsSwiper = new Swiper('.related-products-swiper', {
        slidesPerView: 2,
        spaceBetween: 16,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 24,
            },
        },
    });
});
</script>
@endpush
@endsection



