@php
    $title = $data['title'] ?? 'Tin tức';
    $subtitle = $data['subtitle'] ?? '';
    $displayMode = $data['display_mode'] ?? 'latest';
    $limit = $data['limit'] ?? 6;
    $viewAllLink = $data['view_all_link'] ?? '/bai-viet';

    // Lấy bài viết
    if ($displayMode === 'manual' && !empty($data['posts'])) {
        $postIds = collect($data['posts'])->pluck('post_id')->filter();
        $posts = \App\Models\Post::whereIn('id', $postIds)
            ->where('status', 'active')
            ->get();
    } else {
        $posts = \App\Models\Post::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    $postsCount = $posts->count();
    $featuredPost = $posts->first();
    $remainingPosts = $posts->slice(1);
@endphp

@if($postsCount > 0)
<div class="container mx-auto px-4 relative">
    <!-- Tiêu đề với decorative line -->
    <div class="text-center mb-10 relative">
        <div class="inline-block relative">
            <h2 class="section-title">{{ $title }}</h2>
            <div class="w-full h-1 bg-gradient-to-r from-red-600 via-red-500 to-red-600 absolute -bottom-3 left-0"></div>
            <div class="w-3 h-3 bg-red-600 absolute -bottom-4 left-1/2 transform -translate-x-1/2 rotate-45"></div>
        </div>
        @if($subtitle)
        <p class="section-subtitle mt-6">{{ $subtitle }}</p>
        @endif
    </div>

    <!-- DESKTOP VERSION -->
    <div class="hidden md:block">
        <!-- Featured Post Section -->
        @if($featuredPost)
        <div class="mb-12 group">
            <div class="grid md:grid-cols-5 gap-6 items-center">
                <div class="md:col-span-3 relative overflow-hidden rounded-lg shadow-lg">
                    @if($featuredPost->thumbnail)
                    <div class="relative h-80 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-600/70 to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <img src="{{ asset('storage/' . $featuredPost->thumbnail) }}"
                             alt="{{ $featuredPost->title }}"
                             class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105"
                             loading="lazy">
                        <div class="absolute top-4 left-4 z-20">
                            <span class="bg-red-600 text-white text-xs px-3 py-1.5 rounded-full font-medium tracking-wide">Tin mới nhất</span>
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
                        <span class="inline-flex items-center text-xs bg-red-50 text-red-600 py-1 px-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $featuredPost->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                    <a href="{{ route('posts.show', $featuredPost->slug) }}" class="block group">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-red-600 transition-colors line-clamp-2">{{ $featuredPost->title }}</h3>
                    </a>
                    @if($featuredPost->seo_description)
                    <p class="text-gray-600 mb-6 line-clamp-3">{{ $featuredPost->seo_description }}</p>
                    @endif
                    <a href="{{ route('posts.show', $featuredPost->slug) }}" class="inline-flex items-center text-red-600 hover:text-red-700 font-medium border-b-2 border-red-600/30 hover:border-red-600 pb-0.5 transition-colors group">
                        <span>Đọc chi tiết</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1.5 transform transition-transform group-hover:translate-x-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Remaining Posts Grid -->
        @if($remainingPosts->count() > 0)
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($remainingPosts as $post)
            <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow group hover:translate-y-[-4px] duration-300">
                <a href="{{ route('posts.show', $post->slug) }}" class="block">
                    <div class="h-48 overflow-hidden relative">
                        @if($post->thumbnail)
                        <img src="{{ asset('storage/' . $post->thumbnail) }}"
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        @else
                        <div class="w-full h-full bg-gradient-to-r from-red-100 to-red-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        @endif
                    </div>
                </a>
                <div class="p-5 border-t border-gray-50">
                    <div class="flex items-center mb-2 text-xs">
                        <span class="text-gray-500">{{ $post->created_at->format('d/m/Y') }}</span>
                    </div>
                    <a href="{{ route('posts.show', $post->slug) }}">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-red-600 transition-colors line-clamp-2">{{ $post->title }}</h3>
                    </a>
                    @if($post->seo_description)
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ Str::limit($post->seo_description, 100) }}</p>
                    @endif
                    <div class="flex justify-between items-center pt-2">
                        <a href="{{ route('posts.show', $post->slug) }}" class="inline-flex items-center text-red-600 hover:text-red-700 font-medium text-sm group">
                            <span>Đọc tiếp</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- MOBILE VERSION -->
    <div class="md:hidden">
        <!-- Featured post trên mobile -->
        @if($featuredPost)
        <div class="mb-6 bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100">
            <div class="relative">
                @if($featuredPost->thumbnail)
                <div class="h-52 overflow-hidden">
                    <img src="{{ asset('storage/' . $featuredPost->thumbnail) }}"
                         alt="{{ $featuredPost->title }}"
                         class="w-full h-full object-cover"
                         loading="lazy">
                    <div class="absolute top-0 right-0 bg-red-600 text-white text-xs px-3 py-1 rounded-bl-lg font-medium">Nổi bật</div>
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
                    <span class="inline-flex items-center bg-red-50 text-red-600 py-1 px-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $featuredPost->created_at->format('d/m/Y') }}
                    </span>
                </div>
                <a href="{{ route('posts.show', $featuredPost->slug) }}">
                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $featuredPost->title }}</h3>
                </a>
                @if($featuredPost->seo_description)
                <p class="text-sm text-gray-600 mb-3 line-clamp-3">{{ Str::limit($featuredPost->seo_description, 120) }}</p>
                @endif
                <a href="{{ route('posts.show', $featuredPost->slug) }}" class="flex justify-between items-center pt-2 text-red-600 border-t border-gray-100">
                    <span class="font-medium text-sm">Đọc chi tiết</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
        @endif

        <!-- Remaining posts grid mobile -->
        @if($remainingPosts->count() > 0)
        <div class="grid grid-cols-2 gap-4">
            @foreach($remainingPosts as $post)
            <div class="bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
                <a href="{{ route('posts.show', $post->slug) }}" class="block">
                    <div class="h-32 overflow-hidden">
                        @if($post->thumbnail)
                        <img src="{{ asset('storage/' . $post->thumbnail) }}"
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover"
                             loading="lazy">
                        @else
                        <div class="w-full h-full bg-red-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        @endif
                    </div>
                </a>
                <div class="p-3">
                    <div class="mb-1.5">
                        <span class="text-xs text-gray-500">{{ $post->created_at->format('d/m/Y') }}</span>
                    </div>
                    <a href="{{ route('posts.show', $post->slug) }}">
                        <h3 class="text-sm font-medium text-gray-900 mb-1.5 line-clamp-2 leading-snug">{{ $post->title }}</h3>
                    </a>
                    <a href="{{ route('posts.show', $post->slug) }}" class="inline-flex items-center text-xs text-red-600 font-medium">
                        <span>Đọc tiếp</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- CTA Button -->
    @if($viewAllLink)
    <div class="text-center mt-10">
        <a href="{{ $viewAllLink }}" class="inline-block px-6 md:px-8 py-3 md:py-3.5 bg-white text-red-600 font-medium rounded-full shadow-sm hover:shadow-md border border-red-600 hover:bg-red-600 hover:text-white transition-all duration-300">
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
@else
<div class="container mx-auto px-4">
    <p class="text-center text-gray-500">Chưa có tin tức</p>
</div>
@endif
