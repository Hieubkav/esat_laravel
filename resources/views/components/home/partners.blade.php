@php
    $title = $data['title'] ?? 'Đối tác';
    $subtitle = $data['subtitle'] ?? '';
    $autoScroll = $data['auto_scroll'] ?? true;
    // $partners được truyền từ Controller
@endphp

<div class="container mx-auto px-4">
    <div class="text-center mb-10 md:mb-12">
        <h2 class="section-title mb-6">{{ $title }}</h2>
        @if($subtitle)
        <p class="section-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if($partners->count() > 0)
    <div class="swiper partners-swiper">
        <div class="swiper-wrapper items-center">
            @foreach($partners as $partner)
            <div class="swiper-slide">
                @if(!empty($partner['link']))
                <a href="{{ $partner['link'] }}" target="_blank" rel="noopener" class="block p-4 hover:scale-105 transition-transform">
                @else
                <div class="p-4 hover:scale-105 transition-transform">
                @endif
                    <img src="{{ asset('storage/' . $partner['logo']) }}"
                         alt="{{ $partner['name'] }}"
                         class="h-16 md:h-20 w-auto mx-auto object-contain">
                @if(!empty($partner['link']))
                </a>
                @else
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.partners-swiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: true,
            autoplay: {{ $autoScroll ? '{ delay: 3000, disableOnInteraction: false }' : 'false' }},
            breakpoints: {
                640: { slidesPerView: 3 },
                768: { slidesPerView: 4 },
                1024: { slidesPerView: 5 },
                1280: { slidesPerView: 6 },
            }
        });
    });
    </script>
    @else
    <p class="text-center text-gray-500">Chưa có đối tác</p>
    @endif
</div>
