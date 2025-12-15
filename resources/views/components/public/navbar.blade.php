<header class="bg-white dark:bg-gray-900 shadow-lg sticky top-0 z-50 border-b border-red-100 dark:border-gray-700 backdrop-blur-md bg-white/95 dark:bg-gray-900/95">
    <!-- Main Navigation -->
    <div class="container mx-auto px-4 py-2">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <a href="{{ route('storeFront') }}" class="flex-shrink-0 flex items-center group">
                @if(isset($settings) && !empty($settings) && $settings->logo_link)
                    <div class="h-14 sm:h-16 md:h-17 lg:h-18 flex items-center">
                        <img src="{{ asset('storage/' . $settings->logo_link) }}"
                            alt="{{ $settings->site_name ?? 'ESAT' }}"
                            class="h-auto max-h-full object-contain transition-transform duration-300 group-hover:scale-105"
                            onerror="this.src='{{ asset('images/logo.png') }}'; this.onerror=null;">
                    </div>
                @else
                    <div class="h-14 sm:h-16 md:h-17 lg:h-18 flex items-center">
                        <img src="{{ asset('images/logo.png') }}"
                            alt="ESAT"
                            class="h-auto max-h-full object-contain transition-transform duration-300 group-hover:scale-105">
                    </div>
                @endif
            </a>

            <!-- Thanh tim kiem - Desktop -->
            <div class="hidden lg:block flex-1 max-w-2xl mx-8">
                <form action="{{ route('products.categories') }}" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..."
                        class="w-full px-4 py-2.5 pl-12 pr-4 text-gray-700 bg-gray-50 border border-gray-200 rounded-full focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:bg-white transition-all duration-200"
                        value="{{ request('search') }}">
                    <button type="submit" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Contact Info - Desktop -->
            <div class="hidden lg:flex items-center space-x-3">
                @if(isset($settings) && !empty($settings) && $settings->hotline)
                    <a href="tel:{{ $settings->hotline }}" class="flex items-center px-5 py-2.5 text-red-600 bg-red-50 rounded-full hover:bg-red-100 transition-all duration-200 shadow-sm hover:shadow-md group">
                        <svg class="h-5 w-5 mr-2 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="font-semibold">{{ $settings->hotline }}</span>
                    </a>
                @endif
            </div>

            <!-- Menu mobile (hamburger) -->
            <div class="lg:hidden flex items-center gap-3">
                <!-- Search icon for mobile -->
                <button type="button" class="p-2.5 text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-all duration-200 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Tìm kiếm" id="mobile-search-button">
                    <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="search-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                @if(isset($settings) && !empty($settings) && $settings->hotline)
                    <a href="tel:{{ $settings->hotline }}" class="p-2.5 text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors shadow-sm" aria-label="Gọi điện">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </a>
                @endif

                <button type="button" class="p-2.5 rounded-full text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors shadow-sm" aria-label="Menu" id="mobile-menu-button">
                    <svg class="h-6 w-6 transition-transform duration-200" id="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6 hidden transition-transform duration-200" id="close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="lg:hidden hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg" id="mobile-menu">
        <div class="max-h-[80vh] overflow-y-auto">
            <!-- Thanh tim kiem Mobile -->
            <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-700">
                <form action="{{ route('products.categories') }}" method="GET" class="relative">
                    <input type="text" name="search" id="mobile-search-input" placeholder="Tìm kiếm sản phẩm..."
                        class="w-full px-4 py-3 pl-12 pr-4 text-gray-700 bg-white border border-gray-200 rounded-full focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 shadow-sm"
                        value="{{ request('search') }}">
                    <button type="submit" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Menu Mobile -->
            <div class="py-3 space-y-1">
                <!-- Trang chủ -->
                <a href="{{ route('storeFront') }}"
                   class="flex items-center px-5 py-3 mx-3 text-base font-semibold {{ request()->routeIs('storeFront') ? 'text-white bg-gradient-to-r from-red-600 to-red-700' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-red-600' }} rounded-xl transition-all duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Trang chủ
                </a>

                @if(isset($menuItems) && $menuItems->count() > 0)
                    @foreach($menuItems->where('parent_id', null)->sortBy('order') as $item)
                        @php
                            $hasChildren = $menuItems->where('parent_id', $item->id)->count() > 0;
                            $isActive = request()->url() === $item->getUrl();
                        @endphp
                        @if($hasChildren)
                            <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }" class="mx-3">
                                <button @click="open = !open"
                                        class="flex items-center justify-between w-full px-5 py-3 text-base font-medium {{ $isActive ? 'text-white bg-gradient-to-r from-red-600 to-red-700' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-red-600' }} rounded-xl transition-all duration-200 shadow-sm group">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 {{ $isActive ? 'text-white' : 'text-gray-400 group-hover:text-red-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        {{ $item->label }}
                                    </div>
                                    <svg class="w-5 h-5 transition-transform duration-200 {{ $isActive ? 'text-white' : '' }}" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse x-transition class="mt-2 ml-4 space-y-1 pl-4 border-l-2 border-red-200">
                                    @foreach($menuItems->where('parent_id', $item->id)->sortBy('order') as $child)
                                        @php $isChildActive = request()->url() === $child->getUrl(); @endphp
                                        <a href="{{ $child->getUrl() }}"
                                           class="flex items-center px-4 py-2.5 text-sm {{ $isChildActive ? 'text-white bg-gradient-to-r from-red-500 to-red-600' : 'text-gray-600 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-red-600' }} rounded-lg transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2 {{ $isChildActive ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            {{ $child->label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->getUrl() }}"
                               class="flex items-center px-5 py-3 mx-3 text-base font-medium {{ $isActive ? 'text-white bg-gradient-to-r from-red-600 to-red-700' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-red-600' }} rounded-xl transition-all duration-200 shadow-sm group">
                                <svg class="w-5 h-5 mr-3 {{ $isActive ? 'text-white' : 'text-gray-400 group-hover:text-red-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $item->label }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('products.categories') }}" class="flex items-center px-5 py-3 mx-3 text-base font-medium text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:text-red-600 rounded-xl transition-all duration-200 shadow-sm group">
                     <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                     </svg>
                     Sản phẩm
                    </a>
                    <a href="{{ route('posts.index') }}" class="flex items-center px-5 py-3 mx-3 text-base font-medium text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:text-red-600 rounded-xl transition-all duration-200 shadow-sm group">
                        <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                         </svg>
                         Tin tức
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Menu Navigation Bar - Desktop -->
    <div class="bg-gradient-to-r from-red-600 via-red-600 to-red-700 dark:from-red-700 dark:via-red-700 dark:to-red-800 border-t border-red-500 dark:border-red-600 hidden lg:block shadow-md">
        <div class="container mx-auto px-4">
            <nav class="flex items-center justify-center py-1">
                <!-- Trang chủ -->
                <a href="{{ route('storeFront') }}"
                   class="flex items-center px-5 py-2 text-base font-medium {{ request()->routeIs('storeFront') ? 'text-white bg-red-700/50 dark:bg-red-800/50' : 'text-white hover:bg-red-700/30 dark:hover:bg-red-800/30' }} rounded-lg transition-all duration-200 mx-1">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Trang chủ
                </a>

                @if(isset($menuItems) && $menuItems->count() > 0)
                    @foreach($menuItems->where('parent_id', null)->sortBy('order') as $item)
                        @php
                            $hasChildren = $menuItems->where('parent_id', $item->id)->count() > 0;
                            $isActive = request()->url() === $item->getUrl();
                            $hasActiveChild = false;
                            if ($hasChildren) {
                                foreach ($menuItems->where('parent_id', $item->id) as $child) {
                                    if (request()->url() === $child->getUrl()) {
                                        $hasActiveChild = true;
                                        break;
                                    }
                                }
                            }
                            $isMenuActive = $isActive || $hasActiveChild;
                        @endphp
                        @if($hasChildren)
                            <div class="relative group mx-1">
                                <a href="{{ $item->getUrl() }}"
                                   class="flex items-center px-5 py-2 text-base font-medium {{ $isMenuActive ? 'text-white bg-red-700/50 dark:bg-red-800/50' : 'text-white hover:bg-red-700/30 dark:hover:bg-red-800/30' }} rounded-lg transition-all duration-200">
                                    {{ $item->label }}
                                    <svg class="w-4 h-4 ml-2 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </a>
                                <!-- Dropdown -->
                                <div class="absolute top-full left-1/2 -translate-x-1/2 pt-2 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-red-100 dark:border-red-700 overflow-hidden">
                                        <div class="px-4 py-3 bg-gradient-to-r from-red-50 to-orange-50 dark:from-gray-700 dark:to-gray-600 border-b border-red-100 dark:border-red-700">
                                            <h3 class="text-sm font-bold text-red-700 dark:text-red-300">{{ $item->label }}</h3>
                                        </div>
                                        <div class="py-2">
                                            @foreach($menuItems->where('parent_id', $item->id)->sortBy('order') as $child)
                                                @php $isChildActive = request()->url() === $child->getUrl(); @endphp
                                                <a href="{{ $child->getUrl() }}"
                                                   class="flex items-center px-4 py-3 text-sm {{ $isChildActive ? 'text-white bg-red-600 hover:bg-red-700' : 'text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400' }} transition-all duration-200 group/item">
                                                    <div class="w-2 h-2 {{ $isChildActive ? 'bg-white' : 'bg-red-400 opacity-0 group-hover/item:opacity-100' }} rounded-full mr-3 transition-opacity duration-200"></div>
                                                    {{ $child->label }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->getUrl() }}"
                               class="flex items-center px-5 py-2 text-base font-medium {{ $isActive ? 'text-white bg-red-700/50 dark:bg-red-800/50' : 'text-white hover:bg-red-700/30 dark:hover:bg-red-800/30' }} rounded-lg transition-all duration-200 mx-1">
                                {{ $item->label }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('products.categories') }}" class="flex items-center px-5 py-2 text-base font-medium text-white hover:bg-red-700/30 dark:hover:bg-red-800/30 rounded-lg transition-all duration-200 mx-1">
                         Sản phẩm
                    </a>
                    <a href="{{ route('posts.index') }}" class="flex items-center px-5 py-2 text-base font-medium text-white hover:bg-red-700/30 dark:hover:bg-red-800/30 rounded-lg transition-all duration-200 mx-1">
                        Tin tức
                    </a>
                @endif
            </nav>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');
        const searchButton = document.getElementById('mobile-search-button');

        if (menuButton && mobileMenu) {
            menuButton.addEventListener('click', function() {
                const isHidden = mobileMenu.classList.contains('hidden');
                if (isHidden) {
                    mobileMenu.classList.remove('hidden');
                    mobileMenu.style.animation = 'slideDown 0.3s ease-out';
                    menuIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    mobileMenu.style.animation = 'slideUp 0.3s ease-out';
                    setTimeout(() => {
                        mobileMenu.classList.add('hidden');
                        menuIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        document.body.style.overflow = '';
                    }, 300);
                }
            });
        }

        document.addEventListener('click', function(event) {
            if (mobileMenu && !mobileMenu.contains(event.target) &&
                !menuButton.contains(event.target) &&
                !(searchButton && searchButton.contains(event.target))) {
                if (!mobileMenu.classList.contains('hidden')) {
                    closeMenu();
                }
            }
        });

        function closeMenu() {
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.style.animation = 'slideUp 0.3s ease-out';
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                    if (menuIcon) menuIcon.classList.remove('hidden');
                    if (closeIcon) closeIcon.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 300);
            }
        }

        if (searchButton) {
            searchButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const searchIcon = document.getElementById('search-icon');
                if (searchIcon) {
                    searchIcon.style.transform = 'scale(0.9)';
                    setTimeout(() => { searchIcon.style.transform = 'scale(1)'; }, 150);
                }
                if (mobileMenu) {
                    const isMenuHidden = mobileMenu.classList.contains('hidden');
                    if (isMenuHidden) {
                        mobileMenu.classList.remove('hidden');
                        mobileMenu.style.animation = 'slideDown 0.3s ease-out';
                        if (menuIcon) menuIcon.classList.add('hidden');
                        if (closeIcon) closeIcon.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => {
                            const searchInput = document.getElementById('mobile-search-input');
                            if (searchInput) searchInput.focus();
                        }, 350);
                    } else {
                        const searchInput = document.getElementById('mobile-search-input');
                        if (searchInput) searchInput.focus();
                    }
                }
            });
        }
    });
</script>

<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideUp {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }
    header { transition: all 0.3s ease; }
    .backdrop-blur-md { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
    #mobile-menu::-webkit-scrollbar { width: 4px; }
    #mobile-menu::-webkit-scrollbar-track { background: transparent; }
    #mobile-menu::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.5); border-radius: 2px; }
    #mobile-menu::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.7); }
    #mobile-search-button:active { transform: scale(0.95); }
    #mobile-search-input { -webkit-appearance: none; -moz-appearance: none; appearance: none; }
    @media (max-width: 768px) {
        #mobile-search-input:focus { font-size: 16px; }
        #mobile-search-button { min-width: 44px; min-height: 44px; }
        #mobile-menu { transition: opacity 0.3s ease, transform 0.3s ease; }
    }
    @media (prefers-reduced-motion: reduce) {
        #mobile-menu, #mobile-search-button, #search-icon { animation: none !important; transition: none !important; }
    }
</style>
