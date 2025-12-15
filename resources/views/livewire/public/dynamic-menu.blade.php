<div>
    @if(!$isMobile)
        <!-- Desktop Menu - Horizontal Navigation Bar -->
        <nav class="py-2">
            <div class="flex items-center justify-center space-x-4">
                <!-- Trang chủ luôn hiển thị đầu tiên -->
                <a href="{{ route('storeFront') }}"
                   class="flex items-center px-4 py-1.5 text-base font-medium {{ request()->routeIs('storeFront') ? 'text-white bg-red-700 hover:bg-red-800 dark:bg-red-800 dark:hover:bg-red-900' : 'text-white hover:text-red-100 hover:bg-red-700 dark:hover:bg-red-800' }} rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Trang chủ
                </a>

                @if(isset($menuItems) && $menuItems->count() > 0)
                    @foreach($menuItems as $item)
                        @if(isset($item->children) && $item->children->count() > 0)
                            <!-- Menu có submenu -->
                            @php
                                $hasActiveChild = false;
                                $currentFullUrl = request()->fullUrl();
                                $currentPath = request()->path();
                                $itemUrl = $item->getUrl();
                                $isParentActive = $currentFullUrl === $itemUrl;

                                // Xử lý trường hợp đặc biệt cho menu redirect
                                if (!$isParentActive) {
                                    // Menu "Khóa học" với link "/khoa-hoc" sẽ active khi ở /bai-viet?type=course
                                    if ($item->type === 'link' && $item->link === '/khoa-hoc' &&
                                        $currentPath === 'bai-viet' && request()->get('type') === 'course') {
                                        $isParentActive = true;
                                    }
                                    // Menu "Tin tức" với link "/tin-tuc" sẽ active khi ở /bai-viet?type=news
                                    elseif ($item->type === 'link' && $item->link === '/tin-tuc' &&
                                            $currentPath === 'bai-viet' && request()->get('type') === 'news') {
                                        $isParentActive = true;
                                    }
                                    // Menu "Dịch vụ" với link "/dich-vu" sẽ active khi ở /bai-viet?type=service
                                    elseif ($item->type === 'link' && $item->link === '/dich-vu' &&
                                            $currentPath === 'bai-viet' && request()->get('type') === 'service') {
                                        $isParentActive = true;
                                    }
                                }

                                // Kiểm tra nếu có submenu nào đang active
                                foreach ($item->children as $child) {
                                    $childUrl = $child->getUrl();
                                    if ($currentFullUrl === $childUrl) {
                                        $hasActiveChild = true;
                                        break;
                                    }
                                }

                                $isMenuActive = $isParentActive || $hasActiveChild;
                            @endphp
                            <div class="relative group">
                                @if($item->type === 'display_only')
                                    <!-- Menu chỉ hiển thị (không click được) -->
                                    <button class="flex items-center px-4 py-1.5 text-base font-normal {{ $isMenuActive ? 'text-white bg-red-700 hover:bg-red-800 dark:bg-red-800 dark:hover:bg-red-900' : 'text-white hover:text-red-100 hover:bg-red-700 dark:hover:bg-red-800' }} rounded-lg transition-all duration-200 shadow-sm hover:shadow-md cursor-default">
                                        {{ $item->label }}
                                        <svg class="ml-2 h-4 w-4 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                @else
                                    <!-- Menu có link và submenu -->
                                    <a href="{{ $item->getUrl() }}" class="flex items-center px-4 py-1.5 text-base font-normal {{ $isMenuActive ? 'text-white bg-red-700 hover:bg-red-800 dark:bg-red-800 dark:hover:bg-red-900' : 'text-white hover:text-red-100 hover:bg-red-700 dark:hover:bg-red-800' }} rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                        {{ $item->label }}
                                        <svg class="ml-2 h-4 w-4 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </a>
                                @endif

                                <!-- Dropdown submenu -->
                                <div class="absolute top-full left-1/2 -translate-x-1/2 translate-y-2 group-hover:translate-y-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-red-200 dark:border-red-600 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 backdrop-blur-sm">
                                    <div class="py-3">
                                        <div class="px-4 py-2 border-b border-red-100 dark:border-red-700">
                                            <h3 class="text-sm font-semibold text-red-700 dark:text-red-300">{{ $item->label }}</h3>
                                        </div>
                                        @foreach($item->children as $child)
                                            @php
                                                $isChildActive = request()->url() === $child->getUrl();
                                            @endphp
                                            <a href="{{ $child->getUrl() }}"
                                               class="flex items-center px-4 py-3 text-sm {{ $isChildActive ? 'text-white bg-red-600 hover:bg-red-700' : 'text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400' }} transition-all duration-200 group/item">
                                                <div class="w-2 h-2 {{ $isChildActive ? 'bg-white opacity-100' : 'bg-red-400 opacity-0 group-hover/item:opacity-100' }} rounded-full mr-3 transition-opacity duration-200"></div>
                                                {{ $child->label }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Menu đơn -->
                            @if($item->type === 'display_only')
                                <!-- Menu chỉ hiển thị (không click được) -->
                                <span class="flex items-center px-4 py-1.5 text-base font-normal text-red-200 dark:text-red-300 cursor-default">
                                    {{ $item->label }}
                                </span>
                            @else
                                <!-- Menu có link -->
                                @php
                                    $currentFullUrl = request()->fullUrl();
                                    $currentPath = request()->path();
                                    $itemUrl = $item->getUrl();
                                    $isActive = $currentFullUrl === $itemUrl;

                                    // Xử lý trường hợp đặc biệt cho menu redirect
                                    if (!$isActive) {
                                        // Menu "Khóa học" với link "/khoa-hoc" sẽ active khi ở /bai-viet?type=course
                                        if ($item->type === 'link' && $item->link === '/khoa-hoc' &&
                                            $currentPath === 'bai-viet' && request()->get('type') === 'course') {
                                            $isActive = true;
                                        }
                                        // Menu "Tin tức" với link "/tin-tuc" sẽ active khi ở /bai-viet?type=news
                                        elseif ($item->type === 'link' && $item->link === '/tin-tuc' &&
                                                $currentPath === 'bai-viet' && request()->get('type') === 'news') {
                                            $isActive = true;
                                        }
                                        // Menu "Dịch vụ" với link "/dich-vu" sẽ active khi ở /bai-viet?type=service
                                        elseif ($item->type === 'link' && $item->link === '/dich-vu' &&
                                                $currentPath === 'bai-viet' && request()->get('type') === 'service') {
                                            $isActive = true;
                                        }
                                    }
                                @endphp
                                <a href="{{ $item->getUrl() }}"
                                   class="flex items-center px-4 py-1.5 text-base font-normal {{ $isActive ? 'text-white bg-red-700 hover:bg-red-800 dark:bg-red-800 dark:hover:bg-red-900' : 'text-white hover:text-red-100 hover:bg-red-700 dark:hover:bg-red-800' }} rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                    {{ $item->label }}
                                </a>
                            @endif
                        @endif
                    @endforeach
                @endif

                <!-- Không hiển thị menu mặc định khi không có menu items -->
            </div>
        </nav>
    @else
        <!-- Mobile Menu -->
        <div class="space-y-2">
            <!-- Trang chủ luôn hiển thị đầu tiên -->
            <a href="{{ route('storeFront') }}"
               class="flex items-center px-4 py-3 text-base font-semibold {{ request()->routeIs('storeFront') ? 'text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-red-600 dark:hover:text-red-400' }} rounded-lg mx-2 shadow-md transition-all duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Trang chủ
            </a>

            @if(isset($menuItems) && $menuItems->count() > 0)
                @foreach($menuItems as $item)
                    @if(isset($item->children) && $item->children->count() > 0)
                        <!-- Menu có submenu -->
                        @php
                            $hasActiveChild = false;
                            $currentFullUrl = request()->fullUrl();
                            $currentPath = request()->path();
                            $itemUrl = $item->getUrl();
                            $isParentActive = $currentFullUrl === $itemUrl;

                            // Xử lý trường hợp đặc biệt cho menu redirect
                            if (!$isParentActive) {
                                // Menu "Khóa học" với link "/khoa-hoc" sẽ active khi ở /bai-viet?type=course
                                if ($item->type === 'link' && $item->link === '/khoa-hoc' &&
                                    $currentPath === 'bai-viet' && request()->get('type') === 'course') {
                                    $isParentActive = true;
                                }
                                // Menu "Tin tức" với link "/tin-tuc" sẽ active khi ở /bai-viet?type=news
                                elseif ($item->type === 'link' && $item->link === '/tin-tuc' &&
                                        $currentPath === 'bai-viet' && request()->get('type') === 'news') {
                                    $isParentActive = true;
                                }
                                // Menu "Dịch vụ" với link "/dich-vu" sẽ active khi ở /bai-viet?type=service
                                elseif ($item->type === 'link' && $item->link === '/dich-vu' &&
                                        $currentPath === 'bai-viet' && request()->get('type') === 'service') {
                                    $isParentActive = true;
                                }
                            }

                            // Kiểm tra nếu có submenu nào đang active
                            foreach ($item->children as $child) {
                                $childUrl = $child->getUrl();
                                if ($currentFullUrl === $childUrl) {
                                    $hasActiveChild = true;
                                    break;
                                }
                            }

                            $isMenuActive = $isParentActive || $hasActiveChild;
                        @endphp
                        <div x-data="{ open: {{ $isMenuActive ? 'true' : 'false' }} }" class="mx-2">
                            @if($item->type === 'display_only')
                                <!-- Menu chỉ hiển thị (không click được) -->
                                <button @click="open = !open"
                                        class="w-full text-left px-4 py-3 text-base font-medium {{ $isMenuActive ? 'text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-red-600 dark:hover:text-red-400' }} rounded-lg flex items-center justify-between transition-all duration-200 group cursor-default">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 {{ $isMenuActive ? 'text-white' : 'text-gray-400 group-hover:text-red-500' }} transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        {{ $item->label }}
                                    </div>
                                    <svg class="h-5 w-5 transform transition-transform duration-200 {{ $isMenuActive ? 'text-white' : '' }}" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            @else
                                <!-- Menu có link và submenu -->
                                <button @click="open = !open"
                                        class="w-full text-left px-4 py-3 text-base font-medium {{ $isMenuActive ? 'text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-red-600 dark:hover:text-red-400' }} rounded-lg flex items-center justify-between transition-all duration-200 group">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 {{ $isMenuActive ? 'text-white' : 'text-gray-400 group-hover:text-red-500' }} transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        {{ $item->label }}
                                    </div>
                                    <svg class="h-5 w-5 transform transition-transform duration-200 {{ $isMenuActive ? 'text-white' : '' }}" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            @endif

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="mt-2 ml-4 space-y-1">
                                @foreach($item->children as $child)
                                    @php
                                        $isChildActive = request()->url() === $child->getUrl();
                                    @endphp
                                    <a href="{{ $child->getUrl() }}"
                                       class="flex items-center px-4 py-2 text-sm {{ $isChildActive ? 'text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gradient-to-r hover:from-red-50 hover:to-orange-50 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-red-600 dark:hover:text-red-400' }} rounded-lg transition-all duration-200 group">
                                        <svg class="w-4 h-4 mr-3 {{ $isChildActive ? 'text-white' : 'text-gray-400 group-hover:text-red-500' }} transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Menu đơn -->
                        @if($item->type === 'display_only')
                            <!-- Menu chỉ hiển thị (không click được) -->
                            <span class="flex items-center px-4 py-3 mx-2 text-base font-medium text-gray-500 dark:text-gray-400 cursor-default">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $item->label }}
                            </span>
                        @else
                            <!-- Menu có link -->
                            @php
                                $currentFullUrl = request()->fullUrl();
                                $currentPath = request()->path();
                                $itemUrl = $item->getUrl();
                                $isActive = $currentFullUrl === $itemUrl;

                                // Xử lý trường hợp đặc biệt cho menu redirect
                                if (!$isActive) {
                                    // Menu "Khóa học" với link "/khoa-hoc" sẽ active khi ở /bai-viet?type=course
                                    if ($item->type === 'link' && $item->link === '/khoa-hoc' &&
                                        $currentPath === 'bai-viet' && request()->get('type') === 'course') {
                                        $isActive = true;
                                    }
                                    // Menu "Tin tức" với link "/tin-tuc" sẽ active khi ở /bai-viet?type=news
                                    elseif ($item->type === 'link' && $item->link === '/tin-tuc' &&
                                            $currentPath === 'bai-viet' && request()->get('type') === 'news') {
                                        $isActive = true;
                                    }
                                    // Menu "Dịch vụ" với link "/dich-vu" sẽ active khi ở /bai-viet?type=service
                                    elseif ($item->type === 'link' && $item->link === '/dich-vu' &&
                                            $currentPath === 'bai-viet' && request()->get('type') === 'service') {
                                        $isActive = true;
                                    }
                                }
                            @endphp
                            <a href="{{ $item->getUrl() }}"
                               class="flex items-center px-4 py-3 mx-2 text-base font-medium {{ $isActive ? 'text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-red-600 dark:hover:text-red-400' }} rounded-lg transition-all duration-200 group">
                                <svg class="w-5 h-5 mr-3 {{ $isActive ? 'text-white' : 'text-gray-400 group-hover:text-red-500' }} transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $item->label }}
                            </a>
                        @endif
                    @endif
                @endforeach
            @endif

            <!-- Không hiển thị menu mặc định khi không có menu items -->
        </div>
    @endif
</div>
