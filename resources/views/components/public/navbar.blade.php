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

            <!-- Thanh tìm kiếm - Desktop -->
            <div class="hidden lg:block flex-1 max-w-2xl mx-8">
                <form action="{{ route('products.categories') }}" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..."
                        class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-gray-100 border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        value="{{ request('search') }}">
                    <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Contact Info - Desktop -->
            <div class="hidden lg:flex items-center space-x-3">
                @if(isset($settings) && !empty($settings) && $settings->hotline)
                    <a href="tel:{{ $settings->hotline }}" class="flex items-center px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="font-medium">{{ $settings->hotline }}</span>
                    </a>
                @endif
            </div>

            <!-- Menu mobile (hamburger) -->
            <div class="lg:hidden flex items-center gap-3">
                <!-- Search icon for mobile -->
                <button type="button" class="p-2 text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-all duration-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Tìm kiếm" id="mobile-search-button">
                    <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="search-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                @if(isset($settings) && !empty($settings) && $settings->hotline)
                    <a href="tel:{{ $settings->hotline }}" class="p-2 text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors shadow-sm" aria-label="Gọi điện">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </a>
                @endif

                <button type="button" class="p-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors shadow-sm" aria-label="Menu" id="mobile-menu-button">
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
        <div class="max-h-screen overflow-y-auto">
            <!-- Thanh tìm kiếm Mobile -->
            <div class="p-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <form action="{{ route('products.categories') }}" method="GET" class="relative">
                    <input type="text" name="search" id="mobile-search-input" placeholder="Tìm kiếm sản phẩm..."
                        class="w-full px-4 py-3 pl-10 pr-4 text-gray-700 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        value="{{ request('search') }}">
                    <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Menu Mobile -->
            <div class="py-2">
                <nav class="px-4 space-y-1">
                    @if(isset($menuItems) && $menuItems->count() > 0)
                        @foreach($menuItems->where('parent_id', null)->sortBy('order') as $item)
                            @php
                                $hasChildren = $menuItems->where('parent_id', $item->id)->count() > 0;
                            @endphp
                            @if($hasChildren)
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="flex items-center justify-between w-full py-3 px-4 text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-800 hover:text-red-600 rounded-lg transition-colors">
                                        <span class="font-medium">{{ $item->label }}</span>
                                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-collapse class="pl-4 space-y-1">
                                        @foreach($menuItems->where('parent_id', $item->id)->sortBy('order') as $child)
                                            <a href="{{ $child->getUrl() }}" class="block py-2 px-4 text-gray-600 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-gray-800 hover:text-red-600 rounded-lg transition-colors">
                                                {{ $child->label }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ $item->getUrl() }}" class="block py-3 px-4 text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-800 hover:text-red-600 rounded-lg transition-colors font-medium">
                                    {{ $item->label }}
                                </a>
                            @endif
                        @endforeach
                    @else
                        <a href="{{ route('storeFront') }}" class="block py-3 px-4 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors font-medium">Trang chủ</a>
                        <a href="{{ route('products.categories') }}" class="block py-3 px-4 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors font-medium">Sản phẩm</a>
                        <a href="{{ route('posts.index') }}" class="block py-3 px-4 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors font-medium">Tin tức</a>
                    @endif
                </nav>
            </div>
        </div>
    </div>

    <!-- Menu Navigation Bar - Separate horizontal bar -->
    <div class="bg-red-600 dark:bg-red-700 border-t border-red-500 dark:border-red-600 hidden lg:block">
        <div class="container mx-auto px-4">
            <nav class="flex items-center justify-center space-x-1 py-0">
                @if(isset($menuItems) && $menuItems->count() > 0)
                    @foreach($menuItems->where('parent_id', null)->sortBy('order') as $item)
                        @php
                            $hasChildren = $menuItems->where('parent_id', $item->id)->count() > 0;
                        @endphp
                        @if($hasChildren)
                            <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                                <a href="{{ $item->getUrl() }}" class="flex items-center px-4 py-3 text-white hover:bg-red-700 dark:hover:bg-red-800 transition-colors font-medium">
                                    {{ $item->label }}
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </a>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute left-0 mt-0 w-56 bg-white dark:bg-gray-800 rounded-b-lg shadow-lg z-50">
                                    @foreach($menuItems->where('parent_id', $item->id)->sortBy('order') as $child)
                                        <a href="{{ $child->getUrl() }}" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-red-600 transition-colors">
                                            {{ $child->label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->getUrl() }}" class="px-4 py-3 text-white hover:bg-red-700 dark:hover:bg-red-800 transition-colors font-medium">
                                {{ $item->label }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('storeFront') }}" class="px-4 py-3 text-white hover:bg-red-700 transition-colors font-medium">Trang chủ</a>
                    <a href="{{ route('products.categories') }}" class="px-4 py-3 text-white hover:bg-red-700 transition-colors font-medium">Sản phẩm</a>
                    <a href="{{ route('posts.index') }}" class="px-4 py-3 text-white hover:bg-red-700 transition-colors font-medium">Tin tức</a>
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
