@php
    use Carbon\Carbon;

    // Lấy dữ liệu từ WebDesign
    $blogWebDesign = webDesignData('blog-posts');
    $isVisible = webDesignVisible('blog-posts');

    // Sử dụng dữ liệu từ ViewServiceProvider (4 bài viết tin tức nổi bật)
    $newsPostsData = collect();
    if ($isVisible) {
        $newsPostsData = isset($newsPosts) && !empty($newsPosts) ? $newsPosts : collect();
    }

    $postsCount = $newsPostsData->count();

    // Lấy bài viết nổi bật nhất (bài mới nhất) nếu có dữ liệu
    $featuredPost = $newsPostsData->isNotEmpty() ? $newsPostsData->first() : null;
    // Lấy các bài viết còn lại
    $remainingPosts = $newsPostsData->isNotEmpty() ? $newsPostsData->slice(1) : collect();
@endphp

@if($isVisible && $postsCount > 0)
<div class="container mx-auto px-4 relative">
    <!-- Tiêu đề sáng tạo với gạch chéo -->
    <div class="text-center mb-10 relative">
        <div class="inline-block relative">
            <h2 class="section-title">
                {{ $blogWebDesign?->title ?? 'Tin tức & Sự kiện' }}
            </h2>
            <div class="w-full h-1 bg-gradient-to-r from-red-600 via-red-500 to-red-600 absolute -bottom-3 left-0"></div>
            <div class="w-3 h-3 bg-red-600 absolute -bottom-4 left-1/2 transform -translate-x-1/2 rotate-45"></div>
        </div>
        <p class="section-subtitle mt-6">
            {{ $blogWebDesign?->subtitle ?? 'Cập nhật những tin tức nổi bật và sự kiện mới nhất từ Vũ Phúc Baking' }}
        </p>
    </div>

        <!-- Desktop View với Featured Post + Grid layout -->
        <div class="hidden md:block">
            <!-- Featured Post Section - Bài viết nổi bật -->
            @if($featuredPost)
            <div class="mb-12 group">
                <div class="grid md:grid-cols-5 gap-6 items-center">
                    <div class="md:col-span-3 relative overflow-hidden rounded-lg shadow-lg">
                        @if(isset($featuredPost->thumbnail) && !empty($featuredPost->thumbnail))
                        <div class="relative h-80 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-red-600/70 to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <img
                                src="{{ asset('storage/' . $featuredPost->thumbnail) }}"
                                alt="{{ $featuredPost->title ?? 'Tin tức Vũ Phúc Baking' }}"
                                class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105"
                                loading="lazy"
                            >
                            <div class="absolute top-4 left-4 z-20">
                                <span class="bg-red-600 text-white text-xs px-3 py-1.5 rounded-full font-medium tracking-wide">
                                    Tin mới nhất
                                </span>
                            </div>
                        </div>
                        @else
                        <div class="bg-gradient-to-r from-red-500 to-red-700 h-80 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="md:col-span-2 p-3 md:p-6">
                        <div class="flex items-center mb-3">
                            @if(isset($featuredPost->created_at))
                            <span class="inline-flex items-center text-xs bg-red-50 text-red-600 py-1 px-2 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ Carbon::parse($featuredPost->created_at)->translatedFormat('d/m/Y') }}
                            </span>
                            @endif
                            @if(isset($featuredPost->category) && !empty($featuredPost->category))
                                <span class="mx-2 text-gray-300">•</span>
                                <span class="inline-flex items-center text-xs bg-red-50 text-red-600 py-1 px-2 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    {{ $featuredPost->category->name }}
                                </span>
                            @endif
                        </div>
                        @if(isset($featuredPost->slug))
                        <a href="{{ route('posts.show', $featuredPost->slug) }}" class="block group">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-red-600 transition-colors line-clamp-2">{{ $featuredPost->title ?? 'Tin tức mới' }}</h3>
                        </a>
                        @else
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $featuredPost->title ?? 'Tin tức mới' }}</h3>
                        @endif
                        @php
                            $cleanContent = \App\Helpers\ContentHelper::getCleanExcerpt($featuredPost, 180);
                        @endphp
                        @if($cleanContent)
                        <p class="text-gray-600 mb-6 line-clamp-3">
                            {{ $cleanContent }}
                        </p>
                        @endif
                        @if(isset($featuredPost->slug))
                        <a href="{{ route('posts.show', $featuredPost->slug) }}" class="inline-flex items-center text-red-600 hover:text-red-700 font-medium border-b-2 border-red-600/30 hover:border-red-600 pb-0.5 transition-colors group">
                            <span>Đọc chi tiết</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1.5 transform transition-transform group-hover:translate-x-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Content Grid / Swiper -->
            @if($remainingPosts->count() > 0)
                @if($remainingPosts->count() <= 4)
                    <!-- Grid layout cho 1-4 bài viết còn lại -->
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($remainingPosts as $post)
                            <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow group hover:translate-y-[-4px] duration-300">
                                @if(isset($post->slug))
                                <a href="{{ route('posts.show', $post->slug) }}" class="block">
                                @else
                                <div class="block">
                                @endif
                                    <div class="h-48 overflow-hidden relative">
                                        @if(isset($post->thumbnail) && !empty($post->thumbnail))
                                            <img
                                                src="{{ asset('storage/' . $post->thumbnail) }}"
                                                alt="{{ $post->title ?? 'Tin tức Vũ Phúc Baking' }}"
                                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                                loading="lazy"
                                            >
                                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        @else
                                            <div class="w-full h-full bg-gradient-to-r from-red-100 to-red-50 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                @if(isset($post->slug))
                                </a>
                                @else
                                </div>
                                @endif
                                <div class="p-5 border-t border-gray-50">
                                    <div class="flex items-center mb-2 text-xs">
                                        @if(isset($post->created_at))
                                        <span class="text-gray-500">{{ Carbon::parse($post->created_at)->translatedFormat('d/m/Y') }}</span>
                                        @endif
                                        @if(isset($post->categories) && $post->categories->count() > 0)
                                            <span class="mx-2 text-gray-300">•</span>
                                            <span class="text-red-600">{{ $post->categories->first()->name }}</span>
                                        @endif
                                    </div>
                                    @if(isset($post->slug))
                                    <a href="{{ route('posts.show', $post->slug) }}">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-red-600 transition-colors line-clamp-2">{{ $post->title ?? 'Tin tức mới' }}</h3>
                                    </a>
                                    @else
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $post->title ?? 'Tin tức mới' }}</h3>
                                    @endif
                                    @php
                                        $cleanContent = \App\Helpers\ContentHelper::getCleanExcerpt($post, 100);
                                    @endphp
                                    @if($cleanContent)
                                    <p class="text-gray-600 mb-4 line-clamp-2">
                                        {{ $cleanContent }}
                                    </p>
                                    @endif
                                    @if(isset($post->slug))
                                    <div class="flex justify-between items-center pt-2">
                                        <a href="{{ route('posts.show', $post->slug) }}" class="inline-flex items-center text-red-600 hover:text-red-700 font-medium text-sm group">
                                            <span>Đọc tiếp</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Swiper cho 4+ bài viết trên desktop -->
                    <div class="blog-desktop-carousel">
                        <div class="swiper-container blog-desktop-swiper">
                            <div class="swiper-wrapper">
                                @foreach($remainingPosts as $post)
                                    <div class="swiper-slide">
                                        <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow h-full mx-2 group">
                                            @if(isset($post->slug))
                                            <a href="{{ route('posts.show', $post->slug) }}" class="block">
                                            @else
                                            <div class="block">
                                            @endif
                                                <div class="h-48 overflow-hidden relative">
                                                    @if(isset($post->thumbnail) && !empty($post->thumbnail))
                                                        <img
                                                            src="{{ asset('storage/' . $post->thumbnail) }}"
                                                            alt="{{ $post->title ?? 'Tin tức Vũ Phúc Baking' }}"
                                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                                            loading="lazy"
                                                        >
                                                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                    @else
                                                        <div class="w-full h-full bg-gradient-to-r from-red-100 to-red-50 flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                            @if(isset($post->slug))
                                            </a>
                                            @else
                                            </div>
                                            @endif
                                            <div class="p-5 border-t border-gray-50">
                                                <div class="flex items-center mb-2 text-xs">
                                                    @if(isset($post->created_at))
                                                    <span class="text-gray-500">{{ Carbon::parse($post->created_at)->translatedFormat('d/m/Y') }}</span>
                                                    @endif
                                                    @if($post->categories->isNotEmpty())
                                                        <span class="mx-2 text-gray-300">•</span>
                                                        <span class="text-red-600">{{ $post->categories->first()->name }}</span>
                                                    @endif
                                                </div>
                                                @if(isset($post->slug))
                                                <a href="{{ route('posts.show', $post->slug) }}">
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-red-600 transition-colors line-clamp-2">{{ $post->title ?? 'Tin tức mới' }}</h3>
                                                </a>
                                                @else
                                                <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $post->title ?? 'Tin tức mới' }}</h3>
                                                @endif
                                                @php
                                                    $cleanContent = \App\Helpers\ContentHelper::getCleanExcerpt($post, 100);
                                                @endphp
                                                @if($cleanContent)
                                                <p class="text-gray-600 mb-4 line-clamp-2">
                                                    {{ $cleanContent }}
                                                </p>
                                                @endif
                                                @if(isset($post->slug))
                                                <div class="flex justify-between items-center pt-2">
                                                    <a href="{{ route('posts.show', $post->slug) }}" class="inline-flex items-center text-red-600 hover:text-red-700 font-medium text-sm group">
                                                        <span>Đọc tiếp</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                        </svg>
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Navigation buttons -->
                            <div class="swiper-button-next blog-desktop-next"></div>
                            <div class="swiper-button-prev blog-desktop-prev"></div>

                            <!-- Pagination -->
                            <div class="swiper-pagination blog-desktop-pagination mt-6"></div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Mobile View - Thiết kế thẻ bài viết độc đáo -->
        <div class="md:hidden">
            <div class="overflow-hidden">
                <!-- Featured post trên mobile -->
                @if($featuredPost)
                <div class="mb-6 bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100">
                    <div class="relative">
                        @if(isset($featuredPost->thumbnail) && !empty($featuredPost->thumbnail))
                            <div class="h-52 overflow-hidden">
                                <img
                                    src="{{ asset('storage/' . $featuredPost->thumbnail) }}"
                                    alt="{{ $featuredPost->title ?? 'Tin tức Vũ Phúc Baking' }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                                <div class="absolute top-0 right-0 bg-red-600 text-white text-xs px-3 py-1 rounded-bl-lg font-medium">
                                    Nổi bật
                                </div>
                            </div>
                        @else
                            <div class="h-52 bg-gradient-to-r from-red-500 to-red-700 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex flex-wrap items-center mb-2 text-xs gap-2">
                            @if(isset($featuredPost->created_at))
                            <span class="inline-flex items-center bg-red-50 text-red-600 py-1 px-2 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ Carbon::parse($featuredPost->created_at)->translatedFormat('d/m/Y') }}
                            </span>
                            @endif
                            @if(isset($featuredPost->category) && !empty($featuredPost->category))
                                <span class="inline-flex items-center bg-red-50 text-red-600 py-1 px-2 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    {{ $featuredPost->category->name }}
                                </span>
                            @endif
                        </div>
                        @if(isset($featuredPost->slug))
                        <a href="{{ route('posts.show', $featuredPost->slug) }}">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $featuredPost->title ?? 'Tin tức mới' }}</h3>
                        </a>
                        @else
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $featuredPost->title ?? 'Tin tức mới' }}</h3>
                        @endif
                        @php
                            $cleanContent = \App\Helpers\ContentHelper::getCleanExcerpt($featuredPost, 120);
                        @endphp
                        @if($cleanContent)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-3">
                            {{ $cleanContent }}
                        </p>
                        @endif
                        @if(isset($featuredPost->slug))
                        <a href="{{ route('posts.show', $featuredPost->slug) }}" class="flex justify-between items-center pt-2 text-red-600 border-t border-gray-100">
                            <span class="font-medium text-sm">Đọc chi tiết</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Remaining posts trong swiper -->
                <div class="blog-mobile-carousel">
                    <div class="swiper-container blog-mobile-swiper">
                        <div class="swiper-wrapper">
                            @foreach($remainingPosts as $post)
                                <div class="swiper-slide">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm h-full mx-1.5 border border-gray-100">
                                        @if(isset($post->slug))
                                        <a href="{{ route('posts.show', $post->slug) }}" class="block">
                                        @else
                                        <div class="block">
                                        @endif
                                            <div class="h-36 overflow-hidden">
                                                @if(isset($post->thumbnail) && !empty($post->thumbnail))
                                                    <img
                                                        src="{{ asset('storage/' . $post->thumbnail) }}"
                                                        alt="{{ $post->title ?? 'Tin tức Vũ Phúc Baking' }}"
                                                        class="w-full h-full object-cover"
                                                        loading="lazy"
                                                    >
                                                @else
                                                    <div class="w-full h-full bg-red-50 flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        @if(isset($post->slug))
                                        </a>
                                        @else
                                        </div>
                                        @endif
                                        <div class="p-3">
                                            <div class="mb-1.5">
                                                <div class="flex flex-wrap items-center text-xs gap-2">
                                                    @if(isset($post->created_at))
                                                    <span class="text-gray-500">{{ Carbon::parse($post->created_at)->translatedFormat('d/m/Y') }}</span>
                                                    @endif
                                                    @if($post->categories->isNotEmpty())
                                                        <span class="text-red-600">{{ $post->categories->first()->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(isset($post->slug))
                                            <a href="{{ route('posts.show', $post->slug) }}">
                                                <h3 class="text-base font-medium text-gray-900 mb-1.5 line-clamp-2 leading-snug">{{ $post->title ?? 'Tin tức mới' }}</h3>
                                            </a>
                                            @else
                                            <h3 class="text-base font-medium text-gray-900 mb-1.5 line-clamp-2 leading-snug">{{ $post->title ?? 'Tin tức mới' }}</h3>
                                            @endif
                                            @php
                                                $cleanContent = \App\Helpers\ContentHelper::getCleanExcerpt($post, 70);
                                            @endphp
                                            @if($cleanContent)
                                            <p class="text-xs text-gray-600 mb-3 line-clamp-2">
                                                {{ $cleanContent }}
                                            </p>
                                            @endif
                                            @if(isset($post->slug))
                                            <a href="{{ route('posts.show', $post->slug) }}" class="inline-flex items-center text-xs text-red-600 font-medium">
                                                <span>Đọc tiếp</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination cho mobile -->
                        <div class="swiper-pagination blog-mobile-pagination mt-4"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA nút xem tất cả tin tức -->
        @if($postsCount > 0)
            <div class="text-center mt-10">
                <a href="{{ route('posts.news') }}" class="inline-block px-6 md:px-8 py-3 md:py-3.5 bg-white text-red-600 font-medium rounded-full shadow-sm hover:shadow-md border border-red-600 hover:bg-red-600 hover:text-white transition-all duration-300">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Xem tất cả tin tức
                    </span>
                </a>
            </div>
        @endif
    </div>
</div>
@endif

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<style>
    /* Blog swiper styles */
    .blog-desktop-carousel,
    .blog-mobile-carousel {
        position: relative;
        padding: 0 0.5rem;
    }

    .blog-desktop-swiper .swiper-button-next,
    .blog-desktop-swiper .swiper-button-prev {
        color: white;
        --swiper-navigation-size: 20px;
        background: #dc2626;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        opacity: 0;
        transition: all 0.3s;
    }

    .blog-desktop-carousel:hover .swiper-button-next,
    .blog-desktop-carousel:hover .swiper-button-prev {
        opacity: 0.9;
    }

    .blog-desktop-swiper .swiper-button-next:after,
    .blog-desktop-swiper .swiper-button-prev:after {
        font-size: 14px;
        font-weight: bold;
    }

    .blog-desktop-swiper .swiper-button-next:hover,
    .blog-desktop-swiper .swiper-button-prev:hover {
        background: #b91c1c;
        transform: scale(1.1);
    }

    .blog-desktop-swiper .swiper-pagination,
    .blog-mobile-swiper .swiper-pagination {
        position: relative;
        bottom: 0;
        margin-top: 20px;
    }

    .blog-desktop-swiper .swiper-pagination-bullet,
    .blog-mobile-swiper .swiper-pagination-bullet {
        width: 8px;
        height: 8px;
        transition: all 0.3s;
    }

    .blog-desktop-swiper .swiper-pagination-bullet-active,
    .blog-mobile-swiper .swiper-pagination-bullet-active {
        background: #dc2626;
        width: 20px;
        border-radius: 4px;
    }

    /* Mobile swiper specific */
    .blog-mobile-swiper {
        padding-bottom: 40px;
    }

    /* Line clamp utilities */
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .line-clamp-3 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    /* Hover effects */
    @keyframes pulseRed {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.3);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Swiper cho desktop (4+ bài viết)
        if (document.querySelector('.blog-desktop-swiper')) {
            const desktopSwiperEl = document.querySelector('.blog-desktop-swiper');
            const desktopSlideCount = desktopSwiperEl.querySelectorAll('.swiper-slide').length;
            const blogDesktopSwiper = new Swiper('.blog-desktop-swiper', {
                slidesPerView: 3,
                spaceBetween: 16,
                grabCursor: true,
                loop: desktopSlideCount > 3,
                autoplay: desktopSlideCount > 3 ? {
                    delay: 5000,
                    disableOnInteraction: false,
                } : false,
                pagination: {
                    el: '.blog-desktop-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.blog-desktop-next',
                    prevEl: '.blog-desktop-prev',
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 16,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 24,
                    }
                }
            });
        }

        // Swiper cho mobile
        if (document.querySelector('.blog-mobile-swiper')) {
            const mobileSwiperEl = document.querySelector('.blog-mobile-swiper');
            const mobileSlideCount = mobileSwiperEl.querySelectorAll('.swiper-slide').length;
            const blogMobileSwiper = new Swiper('.blog-mobile-swiper', {
                slidesPerView: 1.2,
                spaceBetween: 12,
                centeredSlides: false,
                loop: mobileSlideCount > 2,
                autoplay: mobileSlideCount > 2 ? {
                    delay: 4000,
                    disableOnInteraction: false,
                } : false,
                pagination: {
                    el: '.blog-mobile-pagination',
                    clickable: true,
                },
                breakpoints: {
                    360: {
                        slidesPerView: 1.3,
                        spaceBetween: 12,
                    },
                    480: {
                        slidesPerView: 1.8,
                        spaceBetween: 16,
                    },
                    640: {
                        slidesPerView: 2.2,
                        spaceBetween: 16,
                    }
                }
            });
        }
    });
</script>
@endpush