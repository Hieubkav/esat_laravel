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
                <form action="{{ route('products.categories') }}" method="GET" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..."
                        class="w-full py-3 pl-12 pr-20 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 shadow-sm hover:shadow-md transition-all duration-200 text-sm"
                        value="{{ request('search') }}"
                        autocomplete="off">
                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg px-3 py-1.5 hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-sm hover:shadow-md text-sm font-medium">
                        Tìm
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
            <div class="p-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <form action="{{ route('products.categories') }}" method="GET" class="relative">
                    <input type="text" name="search" id="mobile-search-input" placeholder="Tìm kiếm sản phẩm..."
                        class="w-full py-3 px-4 pr-12 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 shadow-sm transition-all duration-200"
                        value="{{ request('search') }}"
                        autocomplete="off">
                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-red-600 hover:bg-red-700 text-white rounded-full p-2 transition-colors duration-200 shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Menu Mobile -->
            <div class="py-2">
                @livewire('public.dynamic-menu', ['isMobile' => true])
            </div>
        </div>
    </div>

    <!-- Menu Navigation Bar - Desktop -->
    <div class="bg-red-600 dark:bg-red-700 border-t border-red-500 dark:border-red-600 hidden lg:block">
        <div class="container mx-auto px-4">
            @livewire('public.dynamic-menu', ['isMobile' => false])
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
