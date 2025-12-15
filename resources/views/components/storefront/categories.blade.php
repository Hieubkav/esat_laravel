@php
    // Dữ liệu mẫu cho danh mục sản phẩm
    $categories = collect([
        (object) [
            'id' => 1,
            'name' => 'Nguyên Liệu Làm Bánh',
            'slug' => 'nguyen-lieu-lam-banh',
            'description' => 'Bột mì, đường, trứng, bơ và các nguyên liệu cơ bản khác',
            'image' => 'categories/nguyen-lieu.jpg'
        ],
        (object) [
            'id' => 2,
            'name' => 'Dụng Cụ Làm Bánh',
            'slug' => 'dung-cu-lam-banh',
            'description' => 'Khuôn bánh, dao cắt, máy đánh trứng và các dụng cụ hỗ trợ',
            'image' => 'categories/dung-cu.jpg'
        ],
        (object) [
            'id' => 3,
            'name' => 'Thiết Bị Làm Bánh',
            'slug' => 'thiet-bi-lam-banh',
            'description' => 'Lò nướng, máy trộn bột, tủ ủ bột và các thiết bị chuyên nghiệp',
            'image' => 'categories/thiet-bi.jpg'
        ],
        (object) [
            'id' => 4,
            'name' => 'Phụ Gia & Hương Liệu',
            'slug' => 'phu-gia-huong-lieu',
            'description' => 'Men nướng, bột nở, tinh dầu và các phụ gia chuyên dụng',
            'image' => 'categories/phu-gia.jpg'
        ],
        (object) [
            'id' => 5,
            'name' => 'Trang Trí Bánh',
            'slug' => 'trang-tri-banh',
            'description' => 'Kem tươi, fondant, đường trang trí và các vật dụng trang trí',
            'image' => 'categories/trang-tri.jpg'
        ],
        (object) [
            'id' => 6,
            'name' => 'Bao Bì & Đóng Gói',
            'slug' => 'bao-bi-dong-goi',
            'description' => 'Hộp bánh, túi đựng, giấy gói và các vật liệu đóng gói',
            'image' => 'categories/bao-bi.jpg'
        ]
    ]);
    $categoriesCount = $categories->count();
@endphp

@if($categoriesCount > 0)
<section class="py-12 md:py-16 bg-white" id="categories">
    <div class="container mx-auto px-4">
        <div class="text-center mb-8 md:mb-10">
            <span class="section-badge">Khám phá</span>
            <h2 class="section-title mt-1">Danh Mục Sản Phẩm</h2>
            <div class="w-12 h-0.5 bg-red-600 mx-auto mt-2 mb-3"></div>
            <p class="text-sm text-gray-600 max-w-2xl mx-auto">Vũ Phúc Baking cung cấp đa dạng nguyên liệu, dụng cụ và thiết bị chuyên nghiệp</p>
        </div>

        <!-- Responsive Grid System (for small number of categories) -->
        @if($categoriesCount <= 4)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-{{ $categoriesCount }} xl:grid-cols-{{ $categoriesCount }} gap-6">
            @foreach($categories as $category)
                <a href="{{ route('products.category', $category->slug) }}" class="group category-card">
                    <div class="bg-white rounded-lg overflow-hidden h-full flex flex-col transition-all duration-300 group-hover:shadow border border-gray-100">
                        <div class="aspect-w-5 aspect-h-2 overflow-hidden">
                            @if($category->image)
                                <img
                                    src="{{ asset('storage/' . $category->image) }}"
                                    alt="{{ $category->name }}"
                                    class="w-full h-full object-cover object-center transform transition-transform duration-500 group-hover:scale-105"
                                    onerror="this.style.display='none'; this.parentElement.querySelector('.fallback-bg').style.display='flex';"
                                >
                                <!-- Fallback background khi ảnh không load được -->
                                <div class="fallback-bg w-full h-full bg-gradient-to-r from-red-50 to-red-100 flex items-center justify-center" style="display: none;">
                                    <span class="text-red-600 opacity-80">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4c-2.2 0-4 1.2-4 2.5 0 1.5-2 1.5-2 1.5s-1.5 0-1.5 1.5C4.5 11 5 12 5 12h14s.5-1 .5-2.5c0-1.5-1.5-1.5-1.5-1.5s-2 0-2-1.5C16 5.2 14.2 4 12 4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12v6c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2v-6" />
                                            <circle cx="12" cy="8" r="1" fill="currentColor" />
                                            <circle cx="9" cy="7.5" r="0.75" fill="currentColor" />
                                            <circle cx="15" cy="7.5" r="0.75" fill="currentColor" />
                                        </svg>
                                    </span>
                                </div>
                            @else
                                <div class="w-full h-full bg-gradient-to-r from-red-50 to-red-100 flex items-center justify-center">
                                    <span class="text-red-600 opacity-80">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4c-2.2 0-4 1.2-4 2.5 0 1.5-2 1.5-2 1.5s-1.5 0-1.5 1.5C4.5 11 5 12 5 12h14s.5-1 .5-2.5c0-1.5-1.5-1.5-1.5-1.5s-2 0-2-1.5C16 5.2 14.2 4 12 4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12v6c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2v-6" />
                                            <circle cx="12" cy="8" r="1" fill="currentColor" />
                                            <circle cx="9" cy="7.5" r="0.75" fill="currentColor" />
                                            <circle cx="15" cy="7.5" r="0.75" fill="currentColor" />
                                        </svg>
                                    </span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>

                        <div class="p-4 flex flex-col flex-grow">
                            <h3 class="font-medium text-base text-gray-900 group-hover:text-red-600 transition-colors">
                                {{ $category->name }}
                            </h3>

                            <div class="mt-1 text-xs text-gray-500 line-clamp-1 flex-grow">
                                @if($category->description)
                                    {{ Str::limit($category->description, 60) }}
                                @else
                                    Khám phá các sản phẩm {{ strtolower($category->name) }}
                                @endif
                            </div>

                            <div class="mt-2 text-red-600 text-xs font-medium flex items-center opacity-75 group-hover:opacity-100 transition-opacity">
                                Khám phá
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        @else
        <!-- Dynamic Carousel for 5+ categories -->
        <div class="categories-carousel relative group">
            <!-- Swiper -->
            <div class="swiper-container categories-swiper">
                <div class="swiper-wrapper">
                    @foreach($categories as $category)
                        <div class="swiper-slide category-card">
                            <a href="{{ route('products.category', $category->slug) }}" class="block h-full">
                                <div class="bg-white rounded-lg overflow-hidden h-full flex flex-col transition-all duration-300 hover:shadow border border-gray-100 hover:border-red-100">
                                    <div class="aspect-w-5 aspect-h-2 overflow-hidden">
                                        @if($category->image)
                                            <img
                                                src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}"
                                                class="w-full h-full object-cover object-center transform transition-transform duration-500 hover:scale-105"
                                                onerror="this.style.display='none'; this.parentElement.querySelector('.fallback-bg').style.display='flex';"
                                            >
                                            <!-- Fallback background khi ảnh không load được -->
                                            <div class="fallback-bg w-full h-full bg-gradient-to-r from-red-50 to-red-100 flex items-center justify-center" style="display: none;">
                                                <span class="text-red-600 opacity-80">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4c-2.2 0-4 1.2-4 2.5 0 1.5-2 1.5-2 1.5s-1.5 0-1.5 1.5C4.5 11 5 12 5 12h14s.5-1 .5-2.5c0-1.5-1.5-1.5-1.5-1.5s-2 0-2-1.5C16 5.2 14.2 4 12 4z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12v6c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2v-6" />
                                                        <circle cx="12" cy="8" r="1" fill="currentColor" />
                                                        <circle cx="9" cy="7.5" r="0.75" fill="currentColor" />
                                                        <circle cx="15" cy="7.5" r="0.75" fill="currentColor" />
                                                    </svg>
                                                </span>
                                            </div>
                                        @else
                                            <div class="w-full h-full bg-gradient-to-r from-red-50 to-red-100 flex items-center justify-center">
                                                <span class="text-red-600 opacity-80">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4c-2.2 0-4 1.2-4 2.5 0 1.5-2 1.5-2 1.5s-1.5 0-1.5 1.5C4.5 11 5 12 5 12h14s.5-1 .5-2.5c0-1.5-1.5-1.5-1.5-1.5s-2 0-2-1.5C16 5.2 14.2 4 12 4z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12v6c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2v-6" />
                                                        <circle cx="12" cy="8" r="1" fill="currentColor" />
                                                        <circle cx="9" cy="7.5" r="0.75" fill="currentColor" />
                                                        <circle cx="15" cy="7.5" r="0.75" fill="currentColor" />
                                                    </svg>
                                                </span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                                    </div>

                                    <div class="p-4 flex flex-col flex-grow">
                                        <h3 class="font-medium text-base text-gray-900 hover:text-red-600 transition-colors">
                                            {{ $category->name }}
                                        </h3>

                                        <div class="mt-1 text-xs text-gray-500 line-clamp-1 flex-grow">
                                            @if($category->description)
                                                {{ Str::limit($category->description, 60) }}
                                            @else
                                                Khám phá các sản phẩm {{ strtolower($category->name) }}
                                            @endif
                                        </div>

                                        <div class="mt-2 text-red-600 text-xs font-medium flex items-center opacity-75 hover:opacity-100 transition-opacity">
                                            Khám phá
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1 transition-transform hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Add Pagination -->
                <div class="swiper-pagination mt-6 opacity-70"></div>
            </div>

            <!-- Add Navigation -->
            <div class="swiper-button-next categories-next !bg-white !w-8 !h-8 !rounded-full !shadow-sm !text-red-600 !opacity-0 group-hover:!opacity-90 transition-opacity after:!text-xs"></div>
            <div class="swiper-button-prev categories-prev !bg-white !w-8 !h-8 !rounded-full !shadow-sm !text-red-600 !opacity-0 group-hover:!opacity-90 transition-opacity after:!text-xs"></div>

            <!-- View all link -->
            <div class="text-center mt-8">
                <a href="{{ route('products.categories') }}" class="inline-flex items-center px-5 py-2 border border-red-200 text-red-600 hover:bg-red-50 font-medium rounded transition-colors duration-200">
                    <span>Xem tất cả danh mục</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-2 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- CSS cho section này -->
<style>
    /* Aspect ratio cho ảnh thumbnail */
    .aspect-w-5 {
        position: relative;
        padding-bottom: calc(var(--tw-aspect-h) / var(--tw-aspect-w) * 100%);
    }

    .aspect-w-5 > * {
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }

    .aspect-h-2 {
        --tw-aspect-h: 2;
    }

    .aspect-w-5 {
        --tw-aspect-w: 5;
    }

    /* Line clamp cho phần mô tả */
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Swiper configurations */
    .categories-carousel {
        position: relative;
        padding: 0 0.25rem;
    }

    .swiper-container {
        padding: 0.25rem 0;
    }

    .swiper-slide {
        height: auto;
        transition: transform 0.3s;
    }

    /* Category card styles */
    .category-card {
        height: 100%;
        transition: transform 0.3s ease;
    }

    a.category-card:hover {
        transform: translateY(-2px);
    }

    /* Pagination bullets style */
    .swiper-pagination-bullet {
        width: 6px;
        height: 6px;
        margin: 0 3px;
        background: rgba(0, 0, 0, 0.2);
        opacity: 0.5;
    }

    .swiper-pagination-bullet-active {
        opacity: 1;
        background: #dc2626;
    }

    /* Navigation buttons styles */
    .swiper-button-next:hover,
    .swiper-button-prev:hover {
        background-color: rgba(220, 38, 38, 0.05) !important;
    }
</style>

@push('styles')
<!-- Swiper CSS từ CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endpush

@push('scripts')
<!-- Swiper JS từ CDN -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo Swiper cho categories
        const swiperElement = document.querySelector('.categories-swiper');
        if (swiperElement) {
            const swiper = new Swiper('.categories-swiper', {
                slidesPerView: "auto",
                spaceBetween: 16,
                grabCursor: true,
                threshold: 5,
                centerInsufficientSlides: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    320: {
                        slidesPerView: 1.2,
                        spaceBetween: 12,
                    },
                    480: {
                        slidesPerView: 2,
                        spaceBetween: 16,
                    },
                    640: {
                        slidesPerView: 3,
                        spaceBetween: 16,
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 20,
                    },
                    1280: {
                        slidesPerView: 5,
                        spaceBetween: 20,
                    },
                },
                on: {
                    init: function () {
                        setTimeout(() => {
                            equalizeCardHeights();
                        }, 100);
                    },
                    resize: function() {
                        setTimeout(() => {
                            equalizeCardHeights();
                        }, 100);
                    }
                }
            });

            // Đảm bảo các card có chiều cao đồng đều
            function equalizeCardHeights() {
                const cards = document.querySelectorAll('.category-card');
                let maxHeight = 0;

                // Reset heights
                cards.forEach(card => {
                    card.style.height = 'auto';
                });

                // Find max height
                cards.forEach(card => {
                    if (card.offsetHeight > maxHeight) {
                        maxHeight = card.offsetHeight;
                    }
                });

                // Set all to max height
                if (maxHeight > 0) {
                    cards.forEach(card => {
                        card.style.height = maxHeight + 'px';
                    });
                }
            }

            // Xử lý responsive
            window.addEventListener('resize', equalizeCardHeights);
        }
    });
</script>
@endpush
@endif