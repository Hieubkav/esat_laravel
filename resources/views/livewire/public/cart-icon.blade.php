<div class="relative group">
    <a href="{{ route('mshopkeeper.cart.show') }}" class="relative flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:border-red-300 dark:hover:border-red-500 hover:shadow-lg transition-all duration-300 group" aria-label="Giỏ hàng">
        <!-- Cart Icon -->
        <svg class="h-5 w-5 text-gray-600 dark:text-gray-300 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors duration-300"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>

        <!-- Cart Count Badge -->
        @if($cartCount > 0)
            <span class="absolute -top-2 -right-2 min-w-[20px] h-5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs rounded-full flex items-center justify-center font-semibold shadow-lg border-2 border-white dark:border-gray-900 transform scale-100 group-hover:scale-110 transition-transform duration-200">
                {{ $cartCount > 99 ? '99+' : $cartCount }}
            </span>
        @endif

        <!-- Hover Effect Ring -->
        <div class="absolute inset-0 rounded-full bg-red-500 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
    </a>

    <!-- Enhanced Tooltip -->
    <div class="absolute top-12 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none z-50">
        <!-- Tooltip Arrow -->
        <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-900 dark:border-b-gray-700"></div>
        <div class="bg-gray-900 dark:bg-gray-700 text-white text-sm rounded-lg px-3 py-2 shadow-xl">
            <div class="font-medium">Giỏ hàng</div>
            @if($cartCount > 0)
                <div class="text-xs text-gray-300">{{ $cartCount }} sản phẩm</div>
            @else
                <div class="text-xs text-gray-300">Trống</div>
            @endif
        </div>
    </div>
</div>
