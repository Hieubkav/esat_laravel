<div>
    <!-- Mobile Filter Toggle Button -->
    <div class="lg:hidden mb-6">
        <button onclick="toggleSidebar()" class="w-full bg-red-600 text-white py-3 px-6 rounded-xl hover:bg-red-700 transition-colors font-medium font-open-sans flex items-center justify-center">
            <i class="fas fa-filter mr-2"></i>
            Bộ lọc & Tìm kiếm
        </button>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Desktop Sidebar Filters -->
        <aside class="hidden lg:block w-80 flex-shrink-0">
            <div class="space-y-6" id="desktop-filter-content">
                <!-- Search -->
                <div class="filter-card rounded-xl p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Tìm kiếm</h3>
                    <div class="relative">
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Nhập từ khóa..."
                               class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 font-open-sans text-sm">
                        <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="filter-card rounded-xl p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Chuyên mục</h3>
                    <div class="space-y-1.5">
                        <button wire:click="$set('category', '')"
                               class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ !$category ? 'active' : '' }}">
                            Tất cả chuyên mục
                        </button>
                        @foreach($this->categories as $cat)
                            <button wire:click="$set('category', '{{ $cat->id }}')"
                                   class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ $category == $cat->id ? 'active' : '' }}">
                                {{ $cat->name }}
                                <span class="text-gray-400 text-xs">({{ $cat->posts_count }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Type Filter -->
                <div class="filter-card rounded-xl p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-3 font-montserrat">Nhóm nội dung</h3>
                    <div class="space-y-1.5">
                        <button wire:click="$set('type', '')"
                               class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ !$type ? 'active' : '' }}">
                            Tất cả
                        </button>
                        @foreach($typeNames as $typeKey => $typeName)
                            <button wire:click="$set('type', '{{ $typeKey }}')"
                                   class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ $type === $typeKey ? 'active' : '' }}">
                                {{ $typeName }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Clear Filters -->
                @if($search || $category || $type || $sort !== 'newest')
                <div class="filter-card rounded-xl p-5">
                    <button wire:click="clearFilters"
                           class="block w-full text-center px-3 py-2.5 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors font-medium font-open-sans text-sm">
                        <i class="fas fa-redo mr-2"></i>
                        Xóa bộ lọc
                    </button>
                </div>
                @endif
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1">
            <!-- Active Filters & Results Info -->
            <div class="mb-8 p-6 bg-white rounded-2xl shadow-lg">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Left: Results count and sort info -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center text-gray-600 font-open-sans">
                            <i class="fas fa-file-alt mr-2 text-gray-400"></i>
                            <span class="font-semibold text-gray-900">{{ $totalPosts }}</span>&nbsp; bài viết
                        </div>

                        @php
                            $sortOptions = [
                                'newest' => 'Mới nhất',
                                'oldest' => 'Cũ nhất',
                                'featured' => 'Nổi bật'
                            ];
                        @endphp
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-500 font-open-sans">Sắp xếp:</label>
                            <select wire:model.live="sort"
                                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-red-500 focus:border-red-500 bg-white">
                                @foreach($sortOptions as $sortKey => $sortName)
                                    <option value="{{ $sortKey }}">{{ $sortName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Right: Active filters and clear button -->
                    <div class="flex flex-wrap items-center gap-3">
                        @if($search || $category || $type || $sort !== 'newest')
                            <!-- Active filters -->
                            @if($search)
                                <span class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-800 rounded-full text-xs font-medium font-open-sans">
                                    <i class="fas fa-search mr-1.5"></i>
                                    "{{ Str::limit($search, 20) }}"
                                </span>
                            @endif

                            @if($category)
                                @php
                                    $selectedCategory = $this->categories->where('id', $category)->first();
                                @endphp
                                @if($selectedCategory)
                                    <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-xs font-medium font-open-sans">
                                        <i class="fas fa-folder mr-1.5"></i>
                                        {{ $selectedCategory->name }}
                                    </span>
                                @endif
                            @endif

                            @if($type)
                                <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-xs font-medium font-open-sans">
                                    <i class="fas fa-tag mr-1.5"></i>
                                    {{ $typeNames[$type] }}
                                </span>
                            @endif

                            <!-- Clear filters button -->
                            <button wire:click="clearFilters"
                                   class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-xs font-medium font-open-sans hover:bg-gray-200 transition-colors">
                                <i class="fas fa-times mr-1.5"></i>
                                Xóa bộ lọc
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div wire:loading class="text-center py-8">
                <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg">
                    <i class="fas fa-spinner fa-spin mr-3"></i>
                    Đang tải...
                </div>
            </div>

            <!-- Posts Grid -->
            <div wire:loading.remove>
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
                                                         loading="lazy">
                                                </div>
                                            @else
                                                <!-- Custom placeholder với aspect ratio cố định -->
                                                <div class="aspect-[16/9] w-full bg-gradient-to-br from-red-50 to-red-100 flex flex-col items-center justify-center relative overflow-hidden">
                                                    <!-- Background pattern -->
                                                    <div class="absolute inset-0 opacity-10">
                                                        <svg class="w-full h-full" viewBox="0 0 100 100" fill="none">
                                                            <defs>
                                                                <pattern id="cake-pattern-{{ $post->id }}" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                                                    <circle cx="10" cy="10" r="2" fill="#dc2626"/>
                                                                </pattern>
                                                            </defs>
                                                            <rect width="100" height="100" fill="url(#cake-pattern-{{ $post->id }})"/>
                                                        </svg>
                                                    </div>

                                                    <!-- Cake icon -->
                                                    <div class="relative z-10 text-red-300">
                                                        <svg class="w-16 h-16 mx-auto mb-3" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 6c1.11 0 2-.9 2-2 0-.38-.1-.73-.29-1.03L12 0l-1.71 2.97c-.19.3-.29.65-.29 1.03 0 1.1.89 2 2 2zm4.5 3.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5.67 1.5 1.5 1.5 1.5-.67 1.5-1.5zm-9 0C7.5 8.67 6.83 8 6 8s-1.5.67-1.5 1.5S5.17 11 6 11s1.5-.67 1.5-1.5zM12 15.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM3 13h18c1.1 0 2 .9 2 2v8H1v-8c0-1.1.9-2 2-2z"/>
                                                        </svg>
                                                        <p class="text-sm font-medium font-open-sans">{{ $typeNames[$post->type] ?? 'Bài viết' }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Post Content -->
                                        <div class="p-8">
                                            <!-- Type Badge -->
                                            <div class="flex items-center justify-between mb-4">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 font-open-sans">
                                                    {{ $typeNames[$post->type] ?? 'Bài viết' }}
                                                </span>

                                                @if($post->is_featured)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 font-open-sans">
                                                        <i class="fas fa-star mr-1"></i>
                                                        Nổi bật
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Title -->
                                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-red-600 transition-colors font-montserrat line-clamp-2">
                                                {{ $post->title }}
                                            </h3>

                                            <!-- Excerpt -->
                                            @php
                                                $cleanContent = \App\Helpers\ContentHelper::getCleanExcerpt($post, 150);
                                            @endphp

                                            @if($cleanContent)
                                                <p class="text-gray-600 mb-4 font-open-sans line-clamp-3">
                                                    {{ $cleanContent }}
                                                </p>
                                            @endif

                                            <!-- Meta Info -->
                                            <div class="flex items-center justify-between text-sm text-gray-500 font-open-sans">
                                                <div class="flex items-center">
                                                    <i class="far fa-calendar mr-2"></i>
                                                    {{ $post->created_at->format('d/m/Y') }}
                                                </div>

                                                @if($post->categories->count() > 0)
                                                    <div class="flex items-center">
                                                        <i class="fas fa-folder mr-2"></i>
                                                        {{ $post->categories->pluck('name')->join(', ') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <!-- Load More Button -->
                    @if($hasMorePosts)
                        <div class="flex justify-center">
                            <button wire:click="loadMore"
                                   wire:loading.attr="disabled"
                                   class="inline-flex items-center px-8 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium font-open-sans">
                                <span wire:loading.remove wire:target="loadMore">
                                    <i class="fas fa-chevron-down mr-2"></i>
                                    Xem thêm bài viết
                                </span>
                                <span wire:loading wire:target="loadMore" class="flex items-center">
                                    <i class="fas fa-spinner fa-spin mr-3"></i>
                                    Đang tải...
                                </span>
                            </button>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 font-open-sans">
                                <i class="fas fa-check-circle mr-2"></i>
                                Đã hiển thị tất cả bài viết
                            </p>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="max-w-md mx-auto">
                            <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-search text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 font-montserrat">Không tìm thấy bài viết</h3>
                            <p class="text-gray-600 mb-6 font-open-sans">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm để xem thêm kết quả.</p>
                            <button wire:click="clearFilters"
                                   class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium font-open-sans">
                                <i class="fas fa-redo mr-2"></i>
                                Xem tất cả bài viết
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar lg:hidden" onclick="toggleSidebar()">
        <div class="mobile-sidebar-content" onclick="event.stopPropagation()">
            <div class="p-6">
                <!-- Close button -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 font-montserrat">Bộ lọc</h3>
                    <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Mobile Filter Content (will be populated by JavaScript) -->
                <div id="mobile-filter-content"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleSidebar() {
    const mobileSidebar = document.querySelector('.mobile-sidebar');
    mobileSidebar.classList.toggle('active');
}

// Copy filter content to mobile sidebar
document.addEventListener('DOMContentLoaded', function() {
    const desktopContent = document.getElementById('desktop-filter-content');
    const mobileContent = document.getElementById('mobile-filter-content');

    if (desktopContent && mobileContent) {
        mobileContent.innerHTML = desktopContent.innerHTML;
    }
});
</script>
@endpush