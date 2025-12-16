<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Mobile Filter Button -->
        <div class="lg:hidden mb-6">
            <button @click="$dispatch('toggle-mobile-filter')"
                    class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                <span class="flex items-center font-medium">
                    <i class="fas fa-filter mr-2 text-red-500"></i>
                    Bộ lọc & Tìm kiếm
                </span>
                <i class="fas fa-chevron-down text-gray-400"></i>
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
                            <a href="{{ route('posts.index') }}"
                               class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ !$category ? 'active' : '' }}">
                                Tất cả chuyên mục
                            </a>
                            @foreach($this->categories as $cat)
                                <a href="{{ route('posts.category', $cat->slug) }}"
                                   class="filter-btn block w-full text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ $category == $cat->id ? 'active' : '' }}">
                                    {{ $cat->name }}
                                    <span class="text-gray-400 text-xs">({{ $cat->posts_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    @if($search || $category || $sort !== 'newest')
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

            <!-- Main Content -->
            <main class="flex-1">
                <!-- Active Filters & Results Count -->
                <div class="mb-6">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        @if($search)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-red-100 text-red-800">
                                <i class="fas fa-search mr-1.5"></i>
                                "{{ $search }}"
                                <button wire:click="$set('search', '')" class="ml-2 hover:text-red-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif
                        @if($category)
                            @php $selectedCategory = $this->categories->find($category); @endphp
                            @if($selectedCategory)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-blue-100 text-blue-800">
                                    <i class="fas fa-folder mr-1.5"></i>
                                    {{ $selectedCategory->name }}
                                    <a href="{{ route('posts.index') }}" class="ml-2 hover:text-blue-600">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600 font-open-sans">
                            <span wire:loading.remove>
                                Hiển thị {{ count($posts) }} bài viết
                            </span>
                            <span wire:loading class="flex items-center">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Đang tải...
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-500 font-open-sans">Sắp xếp:</label>
                            <select wire:model.live="sort"
                                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-red-500 focus:border-red-500 bg-white">
                                <option value="newest">Mới nhất</option>
                                <option value="oldest">Cũ nhất</option>
                                <option value="featured">Nổi bật</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Posts Grid -->
                <div wire:loading.remove>
                    @if($posts->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-16 items-stretch">
                            @foreach($posts as $post)
                                <article class="group">
                                    <a href="{{ route('posts.show', $post->slug) }}" class="block h-full">
                                         <div class="post-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 h-full flex flex-col">
                                             <!-- Post Image -->
                                             <div class="w-full h-60 overflow-hidden relative flex-shrink-0">
                                                 @if($post->thumbnail)
                                                     <img src="{{ asset('storage/' . $post->thumbnail) }}"
                                                          alt="{{ $post->title }}"
                                                          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                          loading="lazy">
                                                 @else
                                                     <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 flex flex-col items-center justify-center">
                                                         <div class="text-center">
                                                             <i class="fas fa-newspaper text-4xl text-red-300 mb-2"></i>
                                                             <p class="text-xs text-red-400 font-medium">ESAT</p>
                                                         </div>
                                                     </div>
                                                 @endif

                                                <!-- Badges -->
                                                <div class="absolute top-2 left-2 flex flex-col gap-1">
                                                    @if($post->is_featured)
                                                        <span class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs px-2 py-1 rounded-full font-bold shadow-lg">
                                                            <i class="fas fa-star mr-1"></i>Nổi bật
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Post Info -->
                                            <div class="p-4 flex-grow flex flex-col">
                                                <span class="text-xs text-red-500 font-medium uppercase tracking-wide mb-1 block h-4">
                                                    {{ $post->categories->count() > 0 ? $post->categories->first()->name : '' }}
                                                </span>
                                                <h3 class="text-sm md:text-base font-semibold text-gray-900 group-hover:text-red-700 transition-colors line-clamp-2 mb-3 font-montserrat min-h-[2.5rem] md:min-h-[3rem]">
                                                    {{ $post->title }}
                                                </h3>

                                                <div class="flex items-center justify-between mt-auto">
                                                    <div class="flex items-center text-xs text-gray-500">
                                                        <i class="far fa-calendar mr-1.5"></i>
                                                        {{ $post->created_at->format('d/m/Y') }}
                                                    </div>
                                                    <span class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-red-50 to-red-100 px-3 py-1.5 text-xs font-medium text-red-700 group-hover:from-red-100 group-hover:to-red-200 transition-all">
                                                        Đọc thêm
                                                        <i class="fas fa-arrow-right ml-1"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>

                        <!-- Load More Button -->
                        @if($hasMorePosts)
                            <div class="text-center">
                                <button wire:click="loadMore"
                                        class="inline-flex items-center px-8 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-medium shadow-sm"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="loadMore">
                                        <i class="fas fa-plus mr-2"></i>
                                        Xem thêm bài viết
                                    </span>
                                    <span wire:loading wire:target="loadMore" class="flex items-center">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Đang tải...
                                    </span>
                                </button>
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

                <!-- Loading State -->
                <div wire:loading class="text-center py-16">
                    <div class="inline-flex items-center px-6 py-3 bg-white rounded-xl shadow-sm">
                        <i class="fas fa-spinner fa-spin text-red-500 mr-3"></i>
                        <span class="text-gray-700 font-medium">Đang tải bài viết...</span>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>


