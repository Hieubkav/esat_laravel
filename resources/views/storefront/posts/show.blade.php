@extends('layouts.shop')

@section('title', $post->seo_title ?? $post->title)
@section('description', $post->seo_description ?? Str::limit(strip_tags($post->content), 160))

@if($post->og_image_link)
    @section('og_image', asset('storage/' . $post->og_image_link))
@endif

@push('styles')
<style>
    .prose {
        font-family: 'Open Sans', sans-serif;
        line-height: 1.8;
        color: #374151;
        max-width: none;
    }

    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        color: #1f2937;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .prose h1 { font-size: 2rem; }
    .prose h2 { font-size: 1.75rem; }
    .prose h3 { font-size: 1.5rem; }
    .prose h4 { font-size: 1.25rem; }

    .prose p {
        margin-bottom: 1.5rem;
        text-align: justify;
    }

    .prose ul, .prose ol {
        margin: 1.5rem 0;
        padding-left: 1.5rem;
    }

    .prose li {
        margin: 0.75rem 0;
    }

    .prose strong {
        font-weight: 600;
        color: #1f2937;
    }

    .prose a {
        color: var(--color-primary-600, #dc2626);
        text-decoration: none;
        font-weight: 500;
        border-bottom: 1px solid transparent;
        transition: border-color 0.2s ease;
    }

    .prose a:hover {
        border-bottom-color: var(--color-primary-600, #dc2626);
    }

    .prose img {
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        margin: 2rem auto;
    }

    .prose blockquote {
        border-left: 4px solid var(--color-primary-600, #dc2626);
        background: var(--color-primary-50, #fef2f2);
        padding: 1.5rem;
        margin: 2rem 0;
        border-radius: 0.5rem;
        font-style: italic;
    }
</style>
@endpush

@section('content')
<!-- Minimalist Header -->
<header class="bg-white border-b border-gray-100">
    <div class="container mx-auto px-4 py-6">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 font-open-sans">
                <a href="{{ route('storeFront') }}" class="hover:text-primary-600 transition-colors">
                    <i class="fas fa-home mr-1"></i>Trang chủ
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="{{ route('posts.index') }}" class="hover:text-primary-600 transition-colors">Bài viết</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900">{{ Str::limit($post->title, 40) }}</span>
            </div>
        </nav>

        <!-- Article Header -->
        <div class="max-w-7xl">
            <!-- Type Badge -->
            <div class="flex items-center gap-3 mb-4">
                @if($post->categories->count() > 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                        <i class="fas fa-folder mr-1.5"></i>
                        {{ $post->categories->first()->name }}
                    </span>
                @endif

                @if($post->is_featured)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-star mr-1.5"></i>
                        Nổi bật
                    </span>
                @endif
            </div>

            <!-- Title -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 font-montserrat leading-tight">
                {{ $post->title }}
            </h1>

            <!-- Meta Info -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 font-open-sans">
                <div class="flex items-center">
                    <i class="far fa-calendar mr-2"></i>
                    <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                        {{ $post->created_at->format('d/m/Y') }}
                    </time>
                </div>

                @if($post->categories->count() > 0)
                    <div class="flex items-center">
                        <i class="fas fa-folder mr-2"></i>
                        <span>{{ $post->categories->pluck('name')->join(', ') }}</span>
                    </div>
                @endif

                <div class="flex items-center">
                    <i class="far fa-clock mr-2"></i>
                    <span>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} phút đọc</span>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="bg-white">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-7xl mx-auto">
            <!-- Featured Image - Responsive và thông minh -->
            @if($post->thumbnail && $post->display_thumbnail)
                <div class="mb-12">
                    <div class="relative overflow-hidden rounded-lg shadow-lg">
                        <img src="{{ asset('storage/' . $post->thumbnail) }}"
                             alt="{{ $post->title }}"
                             class="w-full h-auto object-contain max-w-full"
                             style="max-height: none;"
                             loading="eager">
                    </div>
                </div>
            @endif

            <!-- Article Content -->
            <article>
                <x-post-content :post="$post" />
            </article>

            <!-- Additional Images Gallery -->
            @if($post->images && $post->images->where('status', 'active')->count() > 0)
                <div class="mt-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 font-montserrat">Hình ảnh liên quan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($post->images->where('status', 'active')->sortBy('order') as $image)
                            <div class="group cursor-pointer" onclick="openImageModal('{{ asset('storage/' . $image->image_link) }}', '{{ $image->alt_text ?: $post->title }}')">
                                <div class="aspect-video overflow-hidden rounded-lg shadow-lg">
                                    <img src="{{ asset('storage/' . $image->image_link) }}"
                                         alt="{{ $image->alt_text ?: $post->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy">
                                </div>
                                @if($image->alt_text)
                                    <p class="text-sm text-gray-600 mt-2 text-center">{{ $image->alt_text }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        <p id="modalCaption" class="text-white text-center mt-4 text-lg"></p>
    </div>
</div>

<!-- Related Posts -->
@if($relatedPosts->count() > 0)
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 font-montserrat">
                    Bài viết liên quan
                </h3>
                <div class="w-16 h-0.5 bg-primary-600 mx-auto"></div>
            </div>

            <!-- Related Posts Swiper -->
            <div class="related-posts-swiper-container relative">
                <div class="swiper related-posts-swiper">
                    <div class="swiper-wrapper">
                        @foreach($relatedPosts->take(6) as $relatedPost)
                            <div class="swiper-slide">
                                <article class="group h-full">
                                    <a href="{{ route('posts.show', $relatedPost->slug) }}" class="block bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 h-full">
                                        @if($relatedPost->thumbnail)
                                            <div class="h-40 md:h-48 overflow-hidden">
                                                <img src="{{ asset('storage/' . $relatedPost->thumbnail) }}"
                                                     alt="{{ $relatedPost->title }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                     loading="lazy">
                                            </div>
                                        @else
                                            <div class="h-40 md:h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <i class="fas fa-file-alt text-2xl md:text-3xl text-gray-400"></i>
                                            </div>
                                        @endif

                                        <div class="p-3 md:p-4">
                                            <h4 class="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2 mb-2 font-montserrat text-sm md:text-base">
                                                {{ $relatedPost->title }}
                                            </h4>
                                            @php
                                                // Xử lý nội dung để tránh hiển thị HTML entities
                                                $content = '';
                                                if (!empty($relatedPost->content_builder) && is_array($relatedPost->content_builder)) {
                                                    // Lấy nội dung từ content_builder
                                                    foreach ($relatedPost->content_builder as $block) {
                                                        if (isset($block['type']) && $block['type'] === 'paragraph' && isset($block['data']['content'])) {
                                                            $content .= strip_tags(html_entity_decode($block['data']['content'], ENT_QUOTES, 'UTF-8')) . ' ';
                                                        }
                                                    }
                                                } else {
                                                    // Fallback về content thường
                                                    $content = strip_tags(html_entity_decode($relatedPost->content, ENT_QUOTES, 'UTF-8'));
                                                }
                                                $content = trim($content);
                                                // Loại bỏ các ký tự đặc biệt và khoảng trắng thừa
                                                $content = preg_replace('/\s+/', ' ', $content);
                                                $cleanContent = $content ? Str::limit($content, 80) : '';
                                            @endphp

                                            @if($cleanContent)
                                                <p class="text-xs md:text-sm text-gray-600 line-clamp-2 font-open-sans mb-3">
                                                    {{ $cleanContent }}
                                                </p>
                                            @endif

                                            <div class="flex items-center text-xs text-gray-500">
                                                <i class="far fa-calendar mr-1"></i>
                                                <time datetime="{{ $relatedPost->created_at->format('Y-m-d') }}">
                                                    {{ $relatedPost->created_at->format('d/m/Y') }}
                                                </time>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation buttons -->
                <div class="swiper-button-next related-posts-next"></div>
                <div class="swiper-button-prev related-posts-prev"></div>

                <!-- Pagination dots -->
                <div class="swiper-pagination related-posts-pagination"></div>
            </div>

            <!-- View All Button -->
            <div class="text-center">
                <a href="{{ route('posts.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-300 font-open-sans">
                    <span>Xem tất cả</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>
@endif

@push('scripts')
<!-- Swiper đã được load trong layout chính -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Related Posts Swiper
    const relatedPostsSwiper = new Swiper('.related-posts-swiper', {
        slidesPerView: 1.2,
        spaceBetween: 16,
        grabCursor: true,
        loop: false,
        centeredSlides: false,

        // Responsive breakpoints
        breakpoints: {
            480: {
                slidesPerView: 1.5,
                spaceBetween: 20,
            },
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 2.5,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 24,
            },
            1280: {
                slidesPerView: 3.5,
                spaceBetween: 24,
            }
        },

        // Navigation arrows
        navigation: {
            nextEl: '.related-posts-next',
            prevEl: '.related-posts-prev',
        },

        // Pagination
        pagination: {
            el: '.related-posts-pagination',
            clickable: true,
            dynamicBullets: true,
            dynamicMainBullets: 3,
        },

        // Touch settings
        touchRatio: 1,
        touchAngle: 45,
        simulateTouch: true,
        allowTouchMove: true,
        touchStartPreventDefault: false,

        // Mouse wheel
        mousewheel: {
            forceToAxis: true,
            sensitivity: 1,
            releaseOnEdges: true,
        },

        // Keyboard control
        keyboard: {
            enabled: true,
            onlyInViewport: true,
        },

        // Auto height
        autoHeight: false,

        // Smooth transitions
        speed: 400,

        // Prevent clicks during transition
        preventClicks: true,
        preventClicksPropagation: true,
    });
});

// Image Modal Functions
function openImageModal(imageSrc, caption) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');

    modalImage.src = imageSrc;
    modalImage.alt = caption;
    modalCaption.textContent = caption;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endpush

@endsection
