@extends('layouts.shop')
@section('title', $product->name . ' - Vũ Phúc Baking')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('storeFront') }}" class="hover:text-red-600">Trang chủ</a> /
            <a href="{{ route('products.categories') }}" class="hover:text-red-600">Sản phẩm</a> /
            {{ $product->name }}
        </nav>

        <div class="grid lg:grid-cols-2 gap-8">
            @php $images = $product->productImages->where('status', 'active'); $mainImage = $images->first(); @endphp
            <div>
                @if($mainImage)
                    <img id="main-image"
                         src="{{ getProductImageUrlFromImage($mainImage, $product->name) }}"
                         alt="{{ $product->name }}"
                         class="w-full aspect-square object-cover rounded-2xl cursor-pointer hover:scale-105 transition-transform"
                         onclick="openPopup()">

                    @if($images->count() > 1)
                        <div class="grid grid-cols-4 gap-2 mt-4">
                            @foreach($images as $image)
                                <img src="{{ getProductImageUrlFromImage($image, $product->name) }}"
                                     alt="{{ $product->name }}"
                                     class="aspect-square object-cover rounded-lg cursor-pointer border-2 hover:border-red-500 {{ $loop->first ? 'border-red-500' : 'border-gray-200' }}"
                                     onclick="changeImage(this.src, this)">
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl p-6">
                    @if($product->category)
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">{{ $product->category->name }}</span>
                    @endif

                    <h1 class="text-3xl font-bold mt-3 mb-4">{{ $product->name }}</h1>

                    @if($product->is_hot)
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">⭐ Nổi bật</span>
                    @endif

                    <div class="mt-4 space-y-2 text-sm">
                        @if($product->brand)<div>Thương hiệu: <strong>{{ $product->brand }}</strong></div>@endif
                        @if($product->sku)<div>Mã SP: <strong>{{ $product->sku }}</strong></div>@endif
                        @if($product->stock !== null)
                            <div>Tình trạng:
                                <strong class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->stock > 0 ? "Còn hàng ({$product->stock})" : 'Hết hàng' }}
                                </strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-red-50 rounded-2xl p-6 border border-red-200">
                    <div class="flex items-baseline gap-3">
                        @if($product->hasDiscount())
                            <span class="text-3xl font-bold text-red-700">{{ formatPrice($product->getCurrentPrice()) }}</span>
                            <span class="text-lg text-gray-500 line-through">{{ formatPrice($product->price) }}</span>
                            <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full">-{{ $product->getDiscountPercentage() }}%</span>
                        @else
                            <span class="text-3xl font-bold text-red-700">{{ formatPrice($product->price) }}</span>
                        @endif
                    </div>
                    @if($product->unit)<p class="text-sm text-gray-600 mt-2">Đơn vị: {{ $product->unit }}</p>@endif
                </div>

                <div class="bg-white rounded-2xl p-6">
                    <h3 class="font-bold mb-3">Liên hệ đặt hàng</h3>
                    <div class="space-y-2 text-sm">
                        <div>📞 {{ $globalSettings->phone ?? '0123 456 789' }}</div>
                        <div>✉️ {{ $globalSettings->email ?? 'info@vuphucbaking.com' }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($product->description)
            <div class="bg-white rounded-2xl p-6 mt-8">
                <h2 class="text-xl font-bold mb-4">Mô tả sản phẩm</h2>
                <div class="prose max-w-none">{!! $product->description !!}</div>
            </div>
        @endif

        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="bg-white rounded-2xl p-6 mt-8">
                <h2 class="text-xl font-bold mb-6">Sản phẩm liên quan</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($relatedProducts as $relatedProduct)
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="group">
                            <div class="bg-gray-50 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
                                @php $image = $relatedProduct->productImages->first(); @endphp
                                <div class="w-full aspect-square overflow-hidden bg-gray-100 flex items-center justify-center">
                                    @if($image)
                                        <img src="{{ getProductImageUrlFromImage($image, $relatedProduct->name) }}"
                                             alt="{{ $relatedProduct->name }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 flex flex-col items-center justify-center">
                                            <div class="text-center">
                                                <i class="fas fa-birthday-cake text-3xl text-red-300 mb-1"></i>
                                                <p class="text-xs text-red-400 font-medium">Vũ Phúc Baking</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3 class="text-sm font-medium mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                                    <p class="text-sm font-bold text-red-600">{{ formatPrice($relatedProduct->price) }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<div id="popup" class="fixed inset-0 bg-black/75 z-50 hidden items-center justify-center p-4" onclick="closePopup()">
    <div class="relative max-w-4xl max-h-full">
        <img id="popupImg" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        <button onclick="closePopup()" class="absolute top-4 right-4 text-white bg-black/50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-black/75">✕</button>
    </div>
</div>

@push('scripts')
<script>
function changeImage(src, btn) {
    document.getElementById('main-image').src = src;
    document.querySelectorAll('img[onclick*="changeImage"]').forEach(img => img.classList.remove('border-red-500'));
    btn.classList.add('border-red-500');
}

function openPopup() {
    const img = document.getElementById('main-image');
    document.getElementById('popupImg').src = img.src;
    document.getElementById('popup').classList.remove('hidden');
    document.getElementById('popup').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closePopup() {
    document.getElementById('popup').classList.add('hidden');
    document.getElementById('popup').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', e => e.key === 'Escape' && closePopup());
</script>
@endpush
@endsection
