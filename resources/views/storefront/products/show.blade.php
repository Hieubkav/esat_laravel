@extends('layouts.shop')
@section('title', $product->name . ' - ESAT')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('storeFront') }}" class="hover:text-primary-600">Trang chủ</a> /
            <a href="{{ route('products.categories') }}" class="hover:text-primary-600">Sản phẩm</a> /
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
                        <div class="relative mt-4">
                            <div class="swiper thumbnail-swiper">
                                <div class="swiper-wrapper">
                                    @foreach($images as $image)
                                        <div class="swiper-slide">
                                            <img src="{{ getProductImageUrlFromImage($image, $product->name) }}"
                                                 alt="{{ $product->name }}"
                                                 class="aspect-square object-cover rounded-lg cursor-pointer border-2 hover:border-primary-500 transition-colors {{ $loop->first ? 'border-primary-500 active-thumb' : 'border-gray-200' }}"
                                                 onclick="changeImage(this.src, this)">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @if($images->count() > 4)
                                <button class="thumb-prev absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 w-8 h-8 bg-white shadow-md rounded-full flex items-center justify-center hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button class="thumb-next absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 w-8 h-8 bg-white shadow-md rounded-full flex items-center justify-center hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            @endif
                        </div>
                    @endif
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl p-6">
                    @if($product->category)
                        <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm">{{ $product->category->name }}</span>
                    @endif

                    <h1 class="text-3xl font-bold mt-3 mb-4">{{ $product->name }}</h1>

                    @if($product->is_hot)
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">⭐ Nổi bật</span>
                    @endif

                    @if($product->brand)
                    <div class="mt-4 text-sm">
                        <div>Thương hiệu: <strong>{{ $product->brand }}</strong></div>
                    </div>
                    @endif
                </div>

                <div class="bg-primary-50 rounded-2xl p-6 border border-primary-200">
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl font-bold text-primary-700">{{ formatPrice($product->price) }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6">
                    <h3 class="font-bold mb-3">Liên hệ đặt hàng</h3>
                    <div class="space-y-2 text-sm">
                        <div>📞 {{ $globalSettings->hotline ?? '1900636340' }}</div>
                        <div>✉️ {{ $globalSettings->email ?? 'info@esat.vn' }}</div>
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
                                        <div class="w-full h-full bg-gradient-to-br from-primary-50 to-primary-100 flex flex-col items-center justify-center">
                                            <div class="text-center">
                                                <i class="fas fa-birthday-cake text-3xl text-primary-300 mb-1"></i>
                                                <p class="text-xs text-primary-400 font-medium">ESAT</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3 class="text-sm font-medium mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                                    <p class="text-sm font-bold text-primary-600">{{ formatPrice($relatedProduct->price) }}</p>
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
document.addEventListener('DOMContentLoaded', function() {
    const thumbSwiper = new Swiper('.thumbnail-swiper', {
        slidesPerView: 4,
        spaceBetween: 8,
        navigation: {
            nextEl: '.thumb-next',
            prevEl: '.thumb-prev',
        },
    });
});

function changeImage(src, btn) {
    document.getElementById('main-image').src = src;
    document.querySelectorAll('.thumbnail-swiper img').forEach(img => {
        img.classList.remove('border-primary-500');
        img.classList.add('border-gray-200');
    });
    btn.classList.remove('border-gray-200');
    btn.classList.add('border-primary-500');
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
