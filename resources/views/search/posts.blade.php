@extends('layouts.shop')

@section('title', 'Tìm kiếm bài viết: ' . $query)
@section('meta_description', 'Kết quả tìm kiếm bài viết cho từ khóa: ' . $query)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header tìm kiếm -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 py-6">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Kết quả tìm kiếm bài viết
                </h1>
                @if(!empty($query))
                    <p class="text-gray-600 dark:text-gray-400">
                        Từ khóa: <span class="font-semibold text-red-600 dark:text-red-400">"{{ $query }}"</span>
                        @if($total > 0)
                            - Tìm thấy {{ number_format($total) }} kết quả
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            @if(empty($query))
                <!-- Trạng thái chưa tìm kiếm -->
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tìm kiếm bài viết</h3>
                        <p class="text-gray-500 dark:text-gray-400">Nhập từ khóa để tìm kiếm bài viết, tin tức, dịch vụ</p>
                    </div>
                </div>
            @elseif($posts->count() > 0)
                <!-- Hiển thị kết quả -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        <article class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden group">
                            <div class="aspect-video overflow-hidden bg-gray-100 dark:bg-gray-700">
                                @php
                                    $image = $post->thumbnail ?? $post->images->first()?->image_link ?? null;
                                @endphp
                                @if($image)
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="{{ $post->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy"
                                         onerror="this.src='{{ asset('images/no-image.svg') }}'; this.onerror=null;">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    @if($post->categories->isNotEmpty())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                            {{ $post->categories->first()->name }}
                                        </span>
                                    @endif
                                    
                                    @if($post->type)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            @switch($post->type)
                                                @case('service')
                                                    Dịch vụ
                                                    @break
                                                @case('course')
                                                    Khóa học
                                                    @break
                                                @case('news')
                                                    Tin tức
                                                    @break
                                                @default
                                                    Bài viết
                                            @endswitch
                                        </span>
                                    @endif
                                </div>
                                
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                    <a href="{{ route('posts.show', $post->slug) }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                
                                @if($post->excerpt)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                                        {{ $post->excerpt }}
                                    </p>
                                @elseif($post->content)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}
                                    </p>
                                @endif
                                
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                                        {{ $post->created_at->format('d/m/Y') }}
                                    </time>
                                    
                                    <a href="{{ route('posts.show', $post->slug) }}" 
                                       class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium">
                                        Đọc thêm →
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $posts->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Không có kết quả -->
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div class="mx-auto w-16 h-16 mb-4 flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 rounded-full">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.291-1.007-5.824-2.562M15 17H9v-2.25A6.75 6.75 0 0115.75 8.25v.178a6.75 6.75 0 01.75 3.072V17z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Không tìm thấy bài viết</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            Không có bài viết nào phù hợp với từ khóa "<span class="font-semibold">{{ $query }}</span>"
                        </p>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <p>Gợi ý:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Kiểm tra lại chính tả</li>
                                <li>Thử từ khóa khác</li>
                                <li>Sử dụng từ khóa ngắn gọn hơn</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
