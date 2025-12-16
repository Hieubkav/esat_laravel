@php
    $activeSliders = \App\Models\Slider::where('status', 'active')->orderBy('order')->get();
@endphp

@if($activeSliders->count() > 0)
<section class="relative overflow-hidden w-full">
    <div id="hero-slider" class="relative w-full">
        <div class="slider-container w-full overflow-hidden relative">
            @forelse($activeSliders as $index => $slider)
                <div class="slide w-full transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'relative' : 'absolute inset-0' }}"
                     data-slide="{{ $index }}"
                     style="{{ $index === 0 ? 'opacity: 1; z-index: 20;' : 'opacity: 0; z-index: 10;' }}">

                    <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/40 z-10"></div>

                    @if($slider->link)
                        <a href="{{ $slider->link }}" class="absolute top-4 right-4 z-30 p-2 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition-colors duration-300 shadow-lg" aria-label="Xem chi tiết">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    @endif

                    @if($slider->image_link)
                        <div class="w-full h-full image-container">
                            <img src="{{ asset('storage/' . $slider->image_link) }}"
                                 alt="{{ $slider->alt_text ?: $slider->title . ' - ESAT' }}"
                                 class="w-full h-auto object-contain mobile-image"
                                 loading="eager"
                                 fetchpriority="high">
                        </div>
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center">
                            <span class="text-white text-lg font-medium">{{ $slider->title ?? 'ESAT' }}</span>
                        </div>
                    @endif

                    @if($slider->title || $slider->description)
                        <div class="absolute inset-0 z-20 flex items-center md:items-center">
                            <div class="w-full px-4 lg:px-8">
                                <div class="max-w-2xl md:max-w-3xl">
                                    @if($slider->title)
                                        <h2 class="text-white text-xl sm:text-2xl md:text-3xl lg:text-5xl xl:text-6xl font-bold mb-2 md:mb-4 drop-shadow-lg md:drop-shadow-2xl leading-tight">{{ $slider->title }}</h2>
                                    @endif
                                    @if($slider->description)
                                        <p class="text-white text-sm sm:text-base md:text-lg lg:text-xl mb-3 md:mb-6 max-w-md md:max-w-2xl drop-shadow-md md:drop-shadow-lg leading-relaxed">{{ $slider->description }}</p>
                                    @endif
                                    @if($slider->link)
                                        <a href="{{ $slider->link }}" class="inline-flex items-center bg-red-600/90 hover:bg-red-700 text-white px-3 py-1.5 md:px-5 md:py-2.5 lg:px-6 lg:py-3 rounded-md md:rounded-lg transition-colors duration-300 shadow-lg md:shadow-xl backdrop-blur-sm border border-red-500/30 text-sm md:text-base lg:text-lg font-medium">
                                            <span>Xem chi tiết</span>
                                            <svg class="h-3.5 w-3.5 md:h-4 md:w-4 lg:h-5 lg:w-5 ml-1.5 md:ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-transparent to-black/30 z-10"></div>
                    <div class="absolute inset-0 z-20 flex flex-col justify-center items-center text-center p-6 sm:p-8">
                        <h2 class="text-white text-xl sm:text-2xl md:text-3xl lg:text-5xl font-bold mb-2 md:mb-4 drop-shadow-lg md:drop-shadow-2xl">ESAT</h2>
                        <p class="text-white text-sm sm:text-base md:text-lg mb-3 md:mb-6 max-w-md md:max-w-2xl drop-shadow-md md:drop-shadow-lg">Chuyên cung cấp thiết bị điện tử chất lượng cao</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($activeSliders->count() > 1)
            <div class="absolute inset-x-0 top-1/2 transform -translate-y-1/2 flex items-center justify-between px-4 md:px-6 z-30">
                <button id="prev-btn" class="p-2 sm:p-3 rounded-full bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 focus:outline-none transition-colors duration-300 shadow-lg" aria-label="Slide trước">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button id="next-btn" class="p-2 sm:p-3 rounded-full bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 focus:outline-none transition-colors duration-300 shadow-lg" aria-label="Slide tiếp theo">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <div class="absolute bottom-4 sm:bottom-6 left-0 right-0 z-30">
                <div id="dots-container" class="flex items-center justify-center gap-2 sm:gap-3">
                    @foreach($activeSliders as $index => $slider)
                        <button class="dot w-3 h-3 sm:w-4 sm:h-4 rounded-full transition-all duration-300 focus:outline-none relative overflow-hidden shadow-lg {{ $index === 0 ? 'bg-white w-8 sm:w-10' : 'bg-white/50' }}"
                                data-slide="{{ $index }}"
                                aria-label="Đi đến slide {{ $index + 1 }}">
                            <span class="absolute left-0 top-0 h-full bg-white transition-all duration-300 {{ $index === 0 ? 'w-full' : 'w-0' }}"></span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

<style>
@media (max-width: 767px) {
    .mobile-image { object-fit: contain !important; object-position: center; width: 100% !important; height: auto !important; max-height: 100% !important; max-width: 100% !important; display: block; }
    #hero-slider .slide { overflow: hidden; }
    #hero-slider .image-container { position: relative; width: 100%; }
    #hero-slider .slide[style*="opacity: 1"] { position: relative !important; z-index: 20 !important; }
    #hero-slider .slide[style*="opacity: 0"] { position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; z-index: 10 !important; }
}
@media (min-width: 768px) and (max-width: 1023px) {
    .mobile-image { object-fit: contain; object-position: center; width: 100%; height: auto; max-width: 100%; }
    .image-container { height: auto; position: relative; width: 100%; display: flex; justify-content: center; align-items: center; }
}
@media (min-width: 1024px) {
    .mobile-image { object-fit: contain; object-position: center; width: 100%; height: auto; max-width: 100%; }
    .image-container { height: auto; position: relative; width: 100%; display: flex; justify-content: center; align-items: center; }
}
#hero-slider { width: 100vw !important; margin-left: calc(-50vw + 50%) !important; overflow: hidden; transform: translateZ(0); backface-visibility: hidden; }
@media (max-width: 767px) {
    #hero-slider { touch-action: pan-y pinch-zoom; -webkit-overflow-scrolling: touch; }
    .slider-container { -webkit-overflow-scrolling: touch; touch-action: pan-y pinch-zoom; }
}
#hero-slider .slide { transition: opacity 800ms cubic-bezier(0.4, 0, 0.2, 1); will-change: opacity; backface-visibility: hidden; transform: translateZ(0); transition-property: opacity !important; }
@media (max-width: 767px) { #hero-slider .slide { transition-duration: 600ms !important; } }
img.mobile-image { backface-visibility: hidden; transform: translateZ(0); }
#hero-slider .slider-container { transform: translateZ(0); backface-visibility: hidden; contain: layout style paint; isolation: isolate; }
</style>

<script>
const HeroSlider = {
    currentSlide: 0,
    totalSlides: {{ $activeSliders->count() }},
    interval: null,
    container: null,
    slides: [],
    dots: [],
    isTransitioning: false,
    isMobile: window.innerWidth <= 767,

    init() {
        this.container = document.getElementById('hero-slider');
        if (!this.container) return;
        this.slides = this.container.querySelectorAll('.slide');
        this.dots = this.container.querySelectorAll('.dot');
        this.isMobile = window.innerWidth <= 767;
        this.calculateHeight();
        this.setupNavigation();
        this.setupTouchEvents();
        if (this.totalSlides > 1) this.startAutoPlay();
        window.addEventListener('resize', () => this.debounceCalculateHeight());
        this.container.addEventListener('mouseenter', () => this.pauseAutoPlay());
        this.container.addEventListener('mouseleave', () => this.startAutoPlay());
    },

    calculateHeight() {
        if (!this.container) return;
        if (window.innerWidth <= 767) this.calculateFixedHeightForMobile();
        else this.calculateDynamicHeightForDesktop();
    },

    calculateFixedHeightForMobile() {
        requestAnimationFrame(() => {
            this.container.style.height = 'auto';
            const slides = this.container.querySelectorAll('.slide');
            slides.forEach((slide) => { slide.style.position = 'relative'; slide.style.opacity = '1'; slide.style.height = 'auto'; slide.style.display = 'block'; });
            this.container.offsetHeight;
            let maxHeight = 0;
            slides.forEach((slide) => { if (slide.offsetHeight > maxHeight) maxHeight = slide.offsetHeight; });
            if (maxHeight > 0) {
                requestAnimationFrame(() => {
                    this.container.style.height = maxHeight + 'px';
                    slides.forEach((slide, index) => {
                        slide.style.height = maxHeight + 'px'; slide.style.display = 'flex'; slide.style.alignItems = 'center'; slide.style.justifyContent = 'center';
                        if (index === this.currentSlide) { slide.style.position = 'relative'; slide.style.opacity = '1'; slide.style.zIndex = '20'; }
                        else { slide.style.position = 'absolute'; slide.style.top = '0'; slide.style.left = '0'; slide.style.right = '0'; slide.style.opacity = '0'; slide.style.zIndex = '10'; }
                    });
                    this.container.querySelectorAll('.image-container').forEach(c => { c.style.height = '100%'; c.style.display = 'flex'; c.style.alignItems = 'center'; c.style.justifyContent = 'center'; });
                });
            }
        });
    },

    calculateDynamicHeightForDesktop() {
        this.container.style.height = 'auto';
        this.container.querySelectorAll('.slide').forEach((slide, index) => {
            if (index === this.currentSlide) { slide.style.position = 'relative'; slide.style.height = 'auto'; slide.style.display = 'block'; }
            else { slide.style.position = 'absolute'; slide.style.top = '0'; slide.style.left = '0'; slide.style.right = '0'; slide.style.height = '100%'; }
        });
        this.container.querySelectorAll('.image-container').forEach(c => { c.style.height = 'auto'; c.style.display = 'flex'; c.style.alignItems = 'center'; c.style.justifyContent = 'center'; });
    },

    setupNavigation() {
        document.getElementById('prev-btn')?.addEventListener('click', () => { this.prevSlide(); this.resetAutoPlay(); });
        document.getElementById('next-btn')?.addEventListener('click', () => { this.nextSlide(); this.resetAutoPlay(); });
        this.dots.forEach((dot, index) => { dot.addEventListener('click', () => { this.goToSlide(index); this.resetAutoPlay(); }); });
    },

    goToSlide(index) {
        if (this.isTransitioning) return;
        this.isTransitioning = true;
        if (this.slides[this.currentSlide]) {
            requestAnimationFrame(() => { this.slides[this.currentSlide].style.opacity = '0'; this.slides[this.currentSlide].style.zIndex = '10'; });
        }
        this.currentSlide = index;
        if (this.slides[this.currentSlide]) {
            setTimeout(() => { requestAnimationFrame(() => { this.slides[this.currentSlide].style.opacity = '1'; this.slides[this.currentSlide].style.zIndex = '20'; }); }, 50);
        }
        this.updateDots();
        setTimeout(() => { this.isTransitioning = false; }, window.innerWidth <= 767 ? 600 : 800);
    },

    nextSlide() { this.goToSlide((this.currentSlide + 1) % this.totalSlides); },
    prevSlide() { this.goToSlide((this.currentSlide - 1 + this.totalSlides) % this.totalSlides); },

    updateDots() {
        this.dots.forEach((dot, index) => {
            const span = dot.querySelector('span');
            if (index === this.currentSlide) { dot.classList.add('bg-white', 'w-8', 'sm:w-10'); dot.classList.remove('bg-white/50'); span?.classList.add('w-full'); span?.classList.remove('w-0'); }
            else { dot.classList.remove('bg-white', 'w-8', 'sm:w-10'); dot.classList.add('bg-white/50'); span?.classList.remove('w-full'); span?.classList.add('w-0'); }
        });
    },

    startAutoPlay() { if (this.totalSlides > 1) this.interval = setInterval(() => this.nextSlide(), 8000); },
    pauseAutoPlay() { if (this.interval) { clearInterval(this.interval); this.interval = null; } },
    resetAutoPlay() { this.pauseAutoPlay(); this.startAutoPlay(); },
    debounceCalculateHeight() { clearTimeout(this.heightCalculateTimeout); this.heightCalculateTimeout = setTimeout(() => this.calculateHeight(), 100); },

    setupTouchEvents() {
        if (!('ontouchstart' in window)) return;
        let startX = 0, startY = 0, isScrolling = false;
        this.container.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; startY = e.touches[0].clientY; isScrolling = false; }, { passive: true });
        this.container.addEventListener('touchmove', (e) => { if (Math.abs(e.touches[0].clientY - startY) > Math.abs(e.touches[0].clientX - startX)) isScrolling = true; }, { passive: true });
        this.container.addEventListener('touchend', (e) => {
            if (!startX || isScrolling || this.isTransitioning) return;
            const diffX = startX - e.changedTouches[0].clientX;
            if (Math.abs(diffX) > 50) { diffX > 0 ? this.nextSlide() : this.prevSlide(); this.resetAutoPlay(); }
            startX = 0; startY = 0;
        }, { passive: true });
    }
};

document.addEventListener('DOMContentLoaded', () => { if (document.getElementById('hero-slider')) HeroSlider.init(); });
</script>
@else
<section class="bg-gradient-to-br from-red-500 to-red-700 py-20">
    <div class="container mx-auto px-4 text-center text-white">
        <h1 class="text-4xl font-bold mb-4">ESAT</h1>
        <p class="text-xl">Chuyên cung cấp thiết bị điện tử chất lượng cao</p>
    </div>
</section>
@endif
