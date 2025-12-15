@php
    $activeSliders = isset($sliders) && !empty($sliders) ? $sliders : \App\Models\Slider::where('status', 'active')->orderBy('order')->get();
@endphp

@if($activeSliders->count() > 0)
<section class="relative overflow-hidden w-full">
    <div id="hero-slider" class="relative w-full" >
        <!-- Slider Container -->
        <div class="slider-container w-full overflow-hidden relative">

            <!-- Slides -->
            @forelse($activeSliders as $index => $slider)
                <div class="slide w-full transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'relative' : 'absolute inset-0' }}"
                     data-slide="{{ $index }}"
                     style="{{ $index === 0 ? 'opacity: 1; z-index: 20;' : 'opacity: 0; z-index: 10;' }}">

                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/40 z-10"></div>

                    <!-- Link Button -->
                    @if($slider->link)
                        <a href="{{ $slider->link }}" class="absolute top-4 right-4 z-30 p-2 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition-colors duration-300 shadow-lg" aria-label="Xem chi tiết">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    @endif

                    <!-- Image -->
                    @if($slider->image_link)
                        <div class="w-full h-full image-container">
                            <img src="{{ asset('storage/' . $slider->image_link) }}"
                                 alt="{{ $slider->alt_text ?: $slider->title . ' - ESAT' }}"
                                 class="w-full h-auto object-contain mobile-image"
                                 loading="eager" {{-- Thay đổi để tránh lỗi lazy loading --}}
                                 fetchpriority="high" {{-- Thêm để ưu tiên tải ảnh --}}
                                 onerror="console.log('Image failed to load:', this.src); this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center\'><span class=\'text-white text-lg font-medium\'>{{ addslashes($slider->title ?? 'ESAT') }}</span></div>';">
                        </div>
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center">
                            <span class="text-white text-lg font-medium">{{ $slider->title ?? 'ESAT' }}</span>
                        </div>
                    @endif
                    <!-- Text Overlay -->
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
                <!-- Empty State -->
                <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-transparent to-black/30 z-10"></div>
                    <div class="absolute inset-0 z-20 flex flex-col justify-center items-center text-center p-6 sm:p-8">
                        <h2 class="text-white text-xl sm:text-2xl md:text-3xl lg:text-5xl font-bold mb-2 md:mb-4 drop-shadow-lg md:drop-shadow-2xl">ESAT</h2>
                        <p class="text-white text-sm sm:text-base md:text-lg mb-3 md:mb-6 max-w-md md:max-w-2xl drop-shadow-md md:drop-shadow-lg">Thiết bị điện tử chất lượng cao</p>
                        <a href="{{ route('products.categories') ?? '#' }}" class="inline-flex items-center bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-4 py-2 md:px-6 md:py-3 lg:px-8 lg:py-4 rounded-lg transition-colors shadow-lg md:shadow-xl text-base md:text-lg font-medium">
                            <span>Khám phá ngay</span>
                            <svg class="h-4 w-4 md:h-5 md:w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforelse

        </div>

        @if($activeSliders->count() > 1)
            <!-- Navigation Arrows -->
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

            <!-- Dots Navigation -->
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
/* Mobile Image Optimization với fixed height */
@media (max-width: 767px) {
    .mobile-image {
        object-fit: contain !important; /* Đảm bảo không cắt ảnh */
        object-position: center;
        width: 100% !important;
        height: auto !important; /* Để ảnh giữ tỷ lệ */
        max-height: 100% !important; /* Giới hạn trong container */
        max-width: 100% !important;
        display: block;
    }

    /* Slide container với fixed height */
    #hero-slider .slider-container {
        /* Height sẽ được set bởi JavaScript */
    }

    /* Slides với fixed height và vertical centering */
    #hero-slider .slide {
        /* Height và display properties sẽ được set bởi JavaScript */
        overflow: hidden; /* Đảm bảo không bị tràn */
    }

    /* Image container với vertical centering */
    #hero-slider .image-container {
        /* Height và display properties sẽ được set bởi JavaScript */
        position: relative;
        width: 100%;
    }

    /* Đảm bảo slide active hiển thị đúng */
    #hero-slider .slide[style*="opacity: 1"] {
        position: relative !important;
        z-index: 20 !important;
    }

    /* Đảm bảo slides ẩn không ảnh hưởng layout */
    #hero-slider .slide[style*="opacity: 0"] {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 10 !important;
    }
}

/* Tablet Image Optimization */
@media (min-width: 768px) and (max-width: 1023px) {
    .mobile-image {
        object-fit: contain;
        object-position: center;
        width: 100%;
        height: auto;
        max-width: 100%;
    }

    .image-container {
        height: auto;
        position: relative;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}

/* Desktop Image Optimization */
@media (min-width: 1024px) {
    .mobile-image {
        object-fit: contain; /* Thay đổi từ cover sang contain để không cắt ảnh */
        object-position: center;
        width: 100%;
        height: auto; /* Thay đổi từ 100% sang auto để giữ tỷ lệ khung hình */
        max-width: 100%; /* Đảm bảo ảnh không vượt quá chiều rộng container */
    }

    /* Image container */
    .image-container {
        height: auto; /* Thay đổi từ 100% sang auto để tự điều chỉnh theo chiều cao ảnh */
        position: relative;
        width: 100%;
        display: flex; /* Thêm flex để căn giữa ảnh */
        justify-content: center; /* Căn giữa theo chiều ngang */
        align-items: center; /* Căn giữa theo chiều dọc */
    }
}

/* Ensure full width container với performance optimization */
#hero-slider {
    width: 100vw !important;
    margin-left: calc(-50vw + 50%) !important;
    overflow: hidden;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
    backface-visibility: hidden;
    will-change: auto;
}

/* Touch optimization cho mobile */
@media (max-width: 767px) {
    #hero-slider {
        touch-action: pan-y pinch-zoom;
        -webkit-overflow-scrolling: touch;
    }

    .slider-container {
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y pinch-zoom;
    }
}

/* Smooth transitions với hardware acceleration - scoped để tránh conflict */
#hero-slider .slide {
    transition: opacity 800ms cubic-bezier(0.4, 0, 0.2, 1);
    will-change: opacity;
    backface-visibility: hidden;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
    /* Override Tailwind transitions nếu có */
    transition-property: opacity !important;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* Tối ưu cho mobile performance */
@media (max-width: 767px) {
    #hero-slider .slide {
        transition-duration: 600ms !important;
        transition-timing-function: cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
    }
}

/* Fix cho lỗi lazy loading và tối ưu performance */
img.mobile-image {
    backface-visibility: hidden;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
    will-change: auto;
}

/* Container optimization */
#hero-slider .slider-container {
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
    backface-visibility: hidden;
    will-change: auto;
    /* Tối ưu rendering */
    contain: layout style paint;
    isolation: isolate;
}

/* Performance optimizations cho mobile với fixed height */
@media (max-width: 767px) {
    #hero-slider .slider-container {
        /* Giảm complexity cho mobile */
        contain: layout paint;
        /* Tránh layout thrashing khi tính toán chiều cao */
        position: relative;
    }

    /* Tối ưu image rendering trên mobile */
    #hero-slider .mobile-image {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
        /* Tránh layout shift */
        vertical-align: middle;
    }

    /* Tối ưu cho việc đo chiều cao */
    #hero-slider .slide {
        /* Đảm bảo không có margin/padding ảnh hưởng đến measurement */
        box-sizing: border-box;
    }

    /* Smooth transition cho height changes */
    #hero-slider {
        transition: height 0.3s ease-out;
    }
}
</style>

<script>
// Hero Slider JavaScript thuần với mobile optimization
const HeroSlider = {
    currentSlide: 0,
    totalSlides: {{ $activeSliders->count() }},
    interval: null,
    container: null,
    slides: [],
    dots: [],
    isTransitioning: false,
    heightCalculateTimeout: null,
    isMobile: window.innerWidth <= 767,

    init() {
        this.container = document.getElementById('hero-slider');
        if (!this.container) return;

        this.slides = this.container.querySelectorAll('.slide');
        this.dots = this.container.querySelectorAll('.dot');

        // Set initial mobile state
        this.isMobile = window.innerWidth <= 767;

        // Calculate height
        this.calculateHeight();

        // Setup navigation
        this.setupNavigation();

        // Setup touch events cho mobile
        this.setupTouchEvents();

        // Start auto-play if multiple slides
        if (this.totalSlides > 1) {
            this.startAutoPlay();
        }

        // Handle resize với debounce
        window.addEventListener('resize', () => this.debounceCalculateHeight());

        // Pause on hover
        this.container.addEventListener('mouseenter', () => this.pauseAutoPlay());
        this.container.addEventListener('mouseleave', () => this.startAutoPlay());
    },

    calculateHeight() {
        if (!this.container) return;

        // Kiểm tra nếu là mobile
        const isMobile = window.innerWidth <= 767;

        if (isMobile) {
            this.calculateFixedHeightForMobile();
        } else {
            this.calculateDynamicHeightForDesktop();
        }
    },

    calculateFixedHeightForMobile() {
        // Sử dụng requestAnimationFrame để tối ưu performance
        requestAnimationFrame(() => {
            // Reset container height để đo chính xác
            this.container.style.height = 'auto';
            const slides = this.container.querySelectorAll('.slide');

            // Batch DOM operations để tránh layout thrashing
            const originalStyles = [];

            // Lưu styles gốc và tạm thời hiển thị tất cả slides
            slides.forEach((slide, index) => {
                originalStyles[index] = {
                    position: slide.style.position,
                    opacity: slide.style.opacity,
                    height: slide.style.height,
                    zIndex: slide.style.zIndex,
                    display: slide.style.display
                };

                // Set styles để đo chiều cao
                slide.style.position = 'relative';
                slide.style.opacity = '1';
                slide.style.height = 'auto';
                slide.style.zIndex = '1';
                slide.style.display = 'block';
            });

            // Force reflow một lần để đo tất cả slides
            this.container.offsetHeight;

            // Đo chiều cao của từng slide
            let maxHeight = 0;
            slides.forEach((slide) => {
                const slideHeight = slide.offsetHeight;
                if (slideHeight > maxHeight) {
                    maxHeight = slideHeight;
                }
            });

            // Áp dụng chiều cao cố định nếu tìm được
            if (maxHeight > 0) {
                // Batch update tất cả styles
                requestAnimationFrame(() => {
                    this.container.style.height = maxHeight + 'px';

                    slides.forEach((slide, index) => {
                        slide.style.height = maxHeight + 'px';
                        slide.style.display = 'flex';
                        slide.style.alignItems = 'center';
                        slide.style.justifyContent = 'center';

                        if (index === this.currentSlide) {
                            slide.style.position = 'relative';
                            slide.style.opacity = '1';
                            slide.style.zIndex = '20';
                        } else {
                            slide.style.position = 'absolute';
                            slide.style.top = '0';
                            slide.style.left = '0';
                            slide.style.right = '0';
                            slide.style.opacity = '0';
                            slide.style.zIndex = '10';
                        }
                    });

                    // Căn giữa image containers
                    const imageContainers = this.container.querySelectorAll('.image-container');
                    imageContainers.forEach(container => {
                        container.style.height = '100%';
                        container.style.display = 'flex';
                        container.style.alignItems = 'center';
                        container.style.justifyContent = 'center';
                    });
                });
            }
        });
    },

    calculateDynamicHeightForDesktop() {
        // Giữ nguyên logic cũ cho desktop
        this.container.style.height = 'auto';
        const slides = this.container.querySelectorAll('.slide');

        slides.forEach((slide, index) => {
            if (index === this.currentSlide) {
                slide.style.position = 'relative';
                slide.style.height = 'auto';
                slide.style.display = 'block';
                slide.style.alignItems = 'initial';
                slide.style.justifyContent = 'initial';
            } else {
                slide.style.position = 'absolute';
                slide.style.top = '0';
                slide.style.left = '0';
                slide.style.right = '0';
                slide.style.height = '100%';
            }
        });

        const imageContainers = this.container.querySelectorAll('.image-container');
        imageContainers.forEach(container => {
            container.style.height = 'auto';
            container.style.display = 'flex';
            container.style.alignItems = 'center';
            container.style.justifyContent = 'center';
        });
    },

    setupNavigation() {
        // Previous button
        const prevBtn = document.getElementById('prev-btn');
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                this.prevSlide();
                this.resetAutoPlay();
            });
        }

        // Next button
        const nextBtn = document.getElementById('next-btn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                this.nextSlide();
                this.resetAutoPlay();
            });
        }

        // Dots
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                this.goToSlide(index);
                this.resetAutoPlay();
            });
        });
    },

    goToSlide(index) {
        // Prevent multiple rapid transitions
        if (this.isTransitioning) return;
        this.isTransitioning = true;

        // Hide current slide với optimized performance
        if (this.slides[this.currentSlide]) {
            const currentSlide = this.slides[this.currentSlide];

            // Sử dụng requestAnimationFrame để tối ưu performance
            requestAnimationFrame(() => {
                currentSlide.style.opacity = '0';
                currentSlide.style.zIndex = '10';
                currentSlide.classList.remove('relative');
                currentSlide.classList.add('absolute', 'inset-0');
            });
        }

        // Update current slide
        this.currentSlide = index;

        // Show new slide với delay tối ưu
        if (this.slides[this.currentSlide]) {
            const newSlide = this.slides[this.currentSlide];

            // Delay ngắn để đảm bảo smooth transition
            setTimeout(() => {
                requestAnimationFrame(() => {
                    newSlide.style.opacity = '1';
                    newSlide.style.zIndex = '20';
                    newSlide.classList.remove('absolute', 'inset-0');
                    newSlide.classList.add('relative');
                });

                // Không cần recalculate height trên mobile vì đã cố định
                // Chỉ recalculate trên desktop khi cần thiết
                if (window.innerWidth > 767) {
                    this.debounceCalculateHeight();
                }
            }, 50);
        }

        // Update dots
        this.updateDots();

        // Reset transition flag
        const transitionDuration = window.innerWidth <= 767 ? 600 : 800;
        setTimeout(() => {
            this.isTransitioning = false;
        }, transitionDuration);
    },

    nextSlide() {
        const nextIndex = (this.currentSlide + 1) % this.totalSlides;
        this.goToSlide(nextIndex);
    },

    prevSlide() {
        const prevIndex = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        this.goToSlide(prevIndex);
    },

    updateDots() {
        this.dots.forEach((dot, index) => {
            const span = dot.querySelector('span');
            if (index === this.currentSlide) {
                dot.classList.add('bg-white', 'w-8', 'sm:w-10');
                dot.classList.remove('bg-white/50');
                if (span) {
                    span.classList.add('w-full');
                    span.classList.remove('w-0');
                }
            } else {
                dot.classList.remove('bg-white', 'w-8', 'sm:w-10');
                dot.classList.add('bg-white/50');
                if (span) {
                    span.classList.remove('w-full');
                    span.classList.add('w-0');
                }
            }
        });
    },

    startAutoPlay() {
        if (this.totalSlides > 1) {
            this.interval = setInterval(() => this.nextSlide(), 8000);
        }
    },

    pauseAutoPlay() {
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
    },

    resetAutoPlay() {
        this.pauseAutoPlay();
        this.startAutoPlay();
    },

    // Debounced height calculation để tránh tính toán liên tục
    debounceCalculateHeight() {
        if (this.heightCalculateTimeout) {
            clearTimeout(this.heightCalculateTimeout);
        }
        this.heightCalculateTimeout = setTimeout(() => {
            this.handleResize();
        }, 100);
    },

    // Handle resize với logic khác nhau cho mobile/desktop
    handleResize() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= 767;

        // Nếu chuyển từ mobile sang desktop hoặc ngược lại
        if (wasMobile !== this.isMobile) {
            this.calculateHeight();
        } else if (this.isMobile) {
            // Nếu vẫn là mobile, chỉ recalculate nếu cần thiết
            this.calculateFixedHeightForMobile();
        } else {
            // Desktop - recalculate như bình thường
            this.calculateDynamicHeightForDesktop();
        }
    },

    // Tối ưu touch events cho mobile
    setupTouchEvents() {
        if (!('ontouchstart' in window)) return;

        let startX = 0;
        let startY = 0;
        let isScrolling = false;

        this.container.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isScrolling = false;
        }, { passive: true });

        this.container.addEventListener('touchmove', (e) => {
            if (!startX || !startY) return;

            const diffX = Math.abs(e.touches[0].clientX - startX);
            const diffY = Math.abs(e.touches[0].clientY - startY);

            if (diffY > diffX) {
                isScrolling = true;
            }
        }, { passive: true });

        this.container.addEventListener('touchend', (e) => {
            if (!startX || isScrolling || this.isTransitioning) return;

            const endX = e.changedTouches[0].clientX;
            const diffX = startX - endX;

            // Minimum swipe distance
            if (Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    this.nextSlide();
                } else {
                    this.prevSlide();
                }
                this.resetAutoPlay();
            }

            startX = 0;
            startY = 0;
        }, { passive: true });
    }
};

// Initialize when DOM is ready với namespace để tránh conflict
document.addEventListener('DOMContentLoaded', () => {
    // Đảm bảo không conflict với các slider khác
    if (document.getElementById('hero-slider')) {
        HeroSlider.init();
    }
});

// Cleanup khi page unload để tránh memory leaks
window.addEventListener('beforeunload', () => {
    if (HeroSlider.interval) {
        clearInterval(HeroSlider.interval);
    }
    if (HeroSlider.heightCalculateTimeout) {
        clearTimeout(HeroSlider.heightCalculateTimeout);
    }
});
</script>
@endif

