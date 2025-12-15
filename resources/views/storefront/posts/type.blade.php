@extends('layouts.shop')

@section('title', $typeNames[$type] . ' - Vũ Phúc Baking')
@section('description', 'Khám phá tất cả ' . strtolower($typeNames[$type]) . ' chất lượng cao tại Vũ Phúc Baking')

@push('styles')
<style>
    .hero-pattern {
        background-image:
            radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 2px, transparent 2px),
            radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 2px, transparent 2px);
        background-size: 50px 50px;
    }

    .filter-card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    .post-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .post-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-red-600 via-red-700 to-red-800 text-white overflow-hidden">
        <div class="absolute inset-0 hero-pattern"></div>
        <div class="relative py-24 md:py-32">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <!-- Icon -->
                    <div class="mb-8">
                        @if($type === 'service')
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.5"></path>
                                </svg>
                            </div>
                        @elseif($type === 'news')
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>
                        @elseif($type === 'course')
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @else
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-8 leading-tight font-montserrat">
                        {{ $typeNames[$type] }}
                    </h1>

                    <!-- Description -->
                    <p class="text-xl md:text-2xl text-white/90 font-open-sans max-w-2xl mx-auto leading-relaxed">
                        @if($type === 'service')
                            Khám phá các dịch vụ chuyên nghiệp của Vũ Phúc Baking
                        @elseif($type === 'news')
                            Cập nhật tin tức mới nhất từ Vũ Phúc Baking
                        @elseif($type === 'course')
                            Tham gia các khóa học làm bánh chuyên nghiệp tại Vũ Phúc Baking Academy
                        @else
                            Đọc các bài viết hữu ích từ Vũ Phúc Baking
                        @endif
                    </p>

                    <!-- Stats -->
                    <div class="mt-12 flex items-center justify-center">
                        <div class="bg-white/20 backdrop-blur-sm rounded-full px-6 py-3 border border-white/30">
                            <span class="text-white/90 font-open-sans">
                                <span class="font-bold text-white">{{ $posts->total() }}</span> kết quả
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave separator -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="rgb(249 250 251)"/>
            </svg>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 sticky top-0 z-40">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm font-open-sans">
                <a href="{{ route('storeFront') }}" class="flex items-center text-gray-600 hover:text-red-600 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Trang chủ
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-900 font-semibold">{{ $typeNames[$type] }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-16">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Sidebar -->
            <div class="lg:w-1/3 xl:w-1/4">
                <div class="filter-card rounded-3xl shadow-2xl p-8 sticky top-24">
                    <!-- Search Box -->
                    <div class="mb-10">
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900 font-montserrat">Tìm kiếm</h3>
                        </div>
                        <form method="GET" action="{{ request()->url() }}">
                            <div class="relative">
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Tìm kiếm {{ strtolower($typeNames[$type]) }}..."
                                       class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-red-500 font-open-sans text-gray-700 bg-white/50 backdrop-blur-sm transition-all duration-300">
                                <svg class="absolute left-4 top-4 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                @if(request('search'))
                                    <button type="button" onclick="this.form.search.value=''; this.form.submit();" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <!-- Hidden inputs to preserve other filters -->
                            @if(request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                        </form>
                    </div>

                    <!-- Sort Options -->
                    <div class="mb-10">
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900 font-montserrat">Sắp xếp</h3>
                        </div>
                        <div class="space-y-3">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}"
                               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 font-open-sans {{ request('sort', 'newest') === 'newest' ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg transform scale-105' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Mới nhất
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}"
                               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 font-open-sans {{ request('sort') === 'oldest' ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg transform scale-105' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Cũ nhất
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'featured']) }}"
                               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 font-open-sans {{ request('sort') === 'featured' ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg transform scale-105' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                                Nổi bật
                            </a>
                        </div>
                    </div>

                    <!-- Categories Filter -->
                    @php
                        $categories = \App\Models\CatPost::where('status', 'active')
                            ->withCount(['posts' => function($query) use ($type) {
                                $query->where('status', 'active')->where('type', $type);
                            }])
                            ->having('posts_count', '>', 0)
                            ->orderBy('order')
                            ->get();
                    @endphp

                    @if($categories->count() > 0)
                        <div>
                            <div class="flex items-center mb-6">
                                <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 font-montserrat">Danh mục</h3>
                            </div>
                            <div class="space-y-3">
                                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}"
                                   class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-300 font-open-sans {{ !request('category') ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg transform scale-105' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                                    <span>Tất cả</span>
                                    <span class="px-2 py-1 text-xs rounded-full {{ !request('category') ? 'bg-white/20' : 'bg-gray-200' }}">{{ $posts->total() }}</span>
                                </a>
                                @foreach($categories as $category)
                                    <a href="{{ request()->fullUrlWithQuery(['category' => $category->slug]) }}"
                                       class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-300 font-open-sans {{ request('category') === $category->slug ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg transform scale-105' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                                        <span>{{ $category->name }}</span>
                                        <span class="px-2 py-1 text-xs rounded-full {{ request('category') === $category->slug ? 'bg-white/20' : 'bg-gray-200' }}">{{ $category->posts_count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Content Area -->
            <div class="lg:w-2/3 xl:w-3/4">
                <!-- Results Header -->
                <div class="bg-white rounded-3xl shadow-xl p-8 mb-12">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 font-montserrat mb-2">
                                @if(request('search'))
                                    Kết quả tìm kiếm
                                @else
                                    Tất cả {{ $typeNames[$type] }}
                                @endif
                            </h2>
                            @if(request('search'))
                                <p class="text-lg text-gray-600 font-open-sans mb-2">
                                    Từ khóa: <span class="font-semibold text-red-600">"{{ request('search') }}"</span>
                                </p>
                            @endif
                            <div class="flex items-center text-gray-500 font-open-sans">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Tìm thấy <span class="font-semibold text-gray-700">{{ $posts->total() }}</span> kết quả
                            </div>
                        </div>

                        <!-- Quick filters for mobile -->
                        <div class="mt-4 sm:mt-0 flex gap-2">
                            @if(request('search') || request('category'))
                                <a href="{{ request()->url() }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full text-sm transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Xóa bộ lọc
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Posts Grid -->
                @if($posts->count() > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
                        @foreach($posts as $post)
                            <article class="group">
                                <a href="{{ route('posts.show', $post->slug) }}" class="block">
                                    <div class="post-card bg-white rounded-3xl overflow-hidden shadow-xl">
                                        <!-- Post Image - Responsive và thông minh -->
                                        <div class="relative overflow-hidden rounded-t-3xl">
                                            @if($post->thumbnail)
                                                <div class="aspect-[16/9] w-full">
                                                    <img src="{{ asset('storage/' . $post->thumbnail) }}"
                                                         alt="{{ $post->title }}"
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                         loading="lazy"></div>
                                            @else
                                                <!-- Custom placeholder với aspect ratio cố định -->
                                                <div class="aspect-[16/9] w-full bg-gradient-to-br from-red-50 to-red-100 flex flex-col items-center justify-center relative overflow-hidden">
                                                    <!-- Background pattern -->
                                                    <div class="absolute inset-0 opacity-10">
                                                        <div class="absolute top-4 left-4 w-3 h-3 bg-red-200 rounded-full"></div>
                                                        <div class="absolute top-8 right-6 w-2 h-2 bg-red-200 rounded-full"></div>
                                                        <div class="absolute bottom-6 left-8 w-2 h-2 bg-red-200 rounded-full"></div>
                                                        <div class="absolute bottom-4 right-4 w-3 h-3 bg-red-200 rounded-full"></div>
                                                    </div>

                                                    <!-- Main icon -->
                                                    <div class="relative z-10 flex flex-col items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.5 1.5 0 013 15.546V12a9 9 0 0118 0v3.546z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16.5V21m0-4.5c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3s-4.5 4.03-4.5 9 2.015 9 4.5 9z" />
                                                        </svg>
                                                        <span class="text-xs text-red-400 font-medium">{{ Str::limit($post->title, 20) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Post Content -->
                                        <div class="p-6">
                                            <!-- Post Meta -->
                                            <div class="flex items-center gap-4 mb-3">
                                                @if($post->is_featured)
                                                    <span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">NỔI BẬT</span>
                                                @endif
                                                @if($post->categories->isNotEmpty())
                                                    <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-700 rounded-full">{{ $post->categories->first()->name }}</span>
                                                @endif
                                            </div>

                                            <h3 class="font-bold text-gray-900 group-hover:text-red-600 transition-colors line-clamp-2 mb-3 font-montserrat text-xl">
                                                {{ $post->title }}
                                            </h3>

                                            <p class="text-gray-600 line-clamp-3 font-open-sans leading-relaxed mb-4">
                                                {{ Str::limit(strip_tags($post->content), 150) }}
                                            </p>

                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center text-sm text-gray-500">
                                                    <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                                                        {{ $post->created_at->format('d/m/Y') }}
                                                    </time>
                                                </div>

                                                <span class="inline-flex items-center text-red-600 font-medium text-sm group-hover:text-red-700 transition-colors">
                                                    <span>Xem chi tiết</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center">
                        {{ $posts->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="max-w-md mx-auto">
                            <svg class="mx-auto h-24 w-24 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 font-montserrat">
                                Không tìm thấy {{ strtolower($typeNames[$type]) }}
                            </h3>
                            <p class="text-gray-600 font-open-sans">
                                @if(request('search'))
                                    Không có kết quả nào phù hợp với từ khóa "{{ request('search') }}".
                                @else
                                    Hiện tại chưa có {{ strtolower($typeNames[$type]) }} nào.
                                @endif
                            </p>
                            @if(request('search') || request('category'))
                                <a href="{{ request()->url() }}"
                                   class="inline-flex items-center mt-4 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                    <span>Xem tất cả</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
