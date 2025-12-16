@php
    // Sử dụng dữ liệu từ ViewServiceProvider với fallback
    $settingsData = $globalSettings ?? $settings ?? null;

    // Fallback: nếu không có dữ liệu từ ViewServiceProvider, lấy trực tiếp từ model
    if (!$settingsData) {
        try {
            $settingsData = \App\Models\Setting::where('status', 'active')->first();
        } catch (\Exception $e) {
            $settingsData = null;
        }
    }

    // Lấy các trường với tên đúng theo database schema
    $phone = $settingsData ? ($settingsData->hotline ?? '1900636340') : '1900636340';
    $zalo = $settingsData ? ($settingsData->zalo_link ?? null) : null;
    $messenger = $settingsData ? ($settingsData->messenger_link ?? null) : null;
@endphp

<!-- Nút cuộn lên trên -->
<button id="scroll-to-top" class="fixed bottom-80 right-6 z-30 bg-primary-600 text-white rounded-full w-12 h-12 shadow-lg flex items-center justify-center hover:bg-primary-700 focus:outline-none transition-all opacity-0 invisible" aria-label="Cuộn lên trên">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</button>

<!-- Nút cuộn xuống -->
<button id="scroll-to-bottom" class="fixed bottom-80 right-6 z-30 bg-primary-600 text-white rounded-full w-12 h-12 shadow-lg flex items-center justify-center hover:bg-primary-700 focus:outline-none transition-all opacity-0 invisible" aria-label="Cuộn xuống dưới">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V4" />
    </svg>
</button>

<!-- Speedial - Liên hệ nhanh -->
@if(!empty($phone) || (!empty($messenger) && isset($messenger)) || (!empty($zalo) && isset($zalo)))
<div class="fixed bottom-6 right-6 z-40">
    <div class="flex flex-col-reverse items-end space-y-2 space-y-reverse">

        <!-- Gọi điện -->
        @if(!empty($phone))
        <a href="tel:{{ $phone }}" class="speedial-btn group flex items-center rounded-full w-14 h-14 transition-all duration-300 hover:scale-105" aria-label="Gọi điện">
            <div class="flex items-center justify-center w-full">
                <img src="{{ asset('images/icon_phone.webp') }}" alt="Phone" class="h-12 w-12 icon-primary-filter">
            </div>
            <!-- Tooltip -->
            <span class="absolute right-16 bg-gray-800 text-white text-sm px-3 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Gọi ngay: {{ $phone }}
            </span>
        </a>
        @endif

        <!-- Liên hệ qua Messenger -->
        @if(!empty($messenger) && isset($messenger))
        <a href="{{ $messenger }}" target="_blank" class="speedial-btn group flex items-center rounded-full w-14 h-14 transition-all duration-300 hover:scale-105" aria-label="Messenger">
            <div class="flex items-center justify-center w-full">
                <img src="{{ asset('images/icon_messenger.webp') }}" alt="Messenger" class="h-12 w-12 icon-primary-filter">
            </div>
            <!-- Tooltip -->
            <span class="absolute right-16 bg-gray-800 text-white text-sm px-3 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Chat Messenger
            </span>
        </a>
        @endif

        <!-- Liên hệ qua Zalo -->
        @if(!empty($zalo) && isset($zalo))
        <a href="{{ $zalo }}" target="_blank" class="speedial-btn group flex items-center rounded-full w-14 h-14 transition-all duration-300 hover:scale-105" aria-label="Zalo">
            <div class="flex items-center justify-center w-full">
                <img src="{{ asset('images/icon_zalo.webp') }}" alt="Zalo" class="h-12 w-12 icon-primary-filter">
            </div>
            <!-- Tooltip -->
            <span class="absolute right-16 bg-gray-800 text-white text-sm px-3 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Chat Zalo
            </span>
        </a>
        @endif

    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollToTopBtn = document.getElementById('scroll-to-top');
        const scrollToBottomBtn = document.getElementById('scroll-to-bottom');

        // Scroll buttons functionality
        let lastScrollTop = 0;
        let scrollDirection = 'down';

        function toggleScrollButtons() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight;
            const clientHeight = document.documentElement.clientHeight;
            const scrollBottom = scrollHeight - scrollTop - clientHeight;

            // Xác định hướng cuộn
            if (scrollTop > lastScrollTop) {
                scrollDirection = 'down';
            } else if (scrollTop < lastScrollTop) {
                scrollDirection = 'up';
            }
            lastScrollTop = scrollTop;

            // Ẩn cả 2 nút trước
            scrollToTopBtn.classList.add('opacity-0', 'invisible');
            scrollToTopBtn.classList.remove('opacity-100', 'visible');
            scrollToBottomBtn.classList.add('opacity-0', 'invisible');
            scrollToBottomBtn.classList.remove('opacity-100', 'visible');

            // Chỉ hiển thị 1 nút dựa theo hướng cuộn và vị trí
            if (scrollTop > 300) {
                if (scrollDirection === 'up' || scrollBottom < 100) {
                    // Đang cuộn lên hoặc gần cuối trang -> hiện nút lên
                    scrollToTopBtn.classList.remove('opacity-0', 'invisible');
                    scrollToTopBtn.classList.add('opacity-100', 'visible');
                } else if (scrollDirection === 'down' && scrollBottom > 300) {
                    // Đang cuộn xuống và còn nhiều nội dung -> hiện nút xuống
                    scrollToBottomBtn.classList.remove('opacity-0', 'invisible');
                    scrollToBottomBtn.classList.add('opacity-100', 'visible');
                }
            }
        }

        // Smooth scroll to top
        if (scrollToTopBtn) {
            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        // Smooth scroll to bottom
        if (scrollToBottomBtn) {
            scrollToBottomBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: document.documentElement.scrollHeight,
                    behavior: 'smooth'
                });
            });
        }

        // Listen for scroll events
        window.addEventListener('scroll', toggleScrollButtons);

        // Initial check
        toggleScrollButtons();
    });
</script>

<style>
    /* Scroll buttons */
    #scroll-to-top, #scroll-to-bottom {
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px color-mix(in srgb, var(--color-primary-600, #dc2626) 20%, transparent);
        backdrop-filter: blur(10px);
        z-index: 35; /* Thấp hơn speedial để không đè lên */
    }
    #scroll-to-top:hover, #scroll-to-bottom:hover {
        box-shadow: 0 6px 16px color-mix(in srgb, var(--color-primary-600, #dc2626) 30%, transparent);
        transform: translateY(-2px);
    }
    #scroll-to-top.visible, #scroll-to-bottom.visible {
        animation: fadeInUp 0.3s ease forwards;
    }

    /* Speedial buttons */
    .speedial-btn {
        animation: slideInUp 0.4s ease forwards;
        transform: translateY(20px);
        opacity: 0;
        position: relative;
        z-index: 41; /* Cao hơn container để tooltip hiển thị đúng */
    }
    .speedial-btn:nth-child(1) {
        animation-delay: 0.1s;
    }
    .speedial-btn:nth-child(2) {
        animation-delay: 0.2s;
    }
    .speedial-btn:nth-child(3) {
        animation-delay: 0.3s;
    }

    /* Đảm bảo spacing giữa các speedial buttons */
    .speedial-btn + .speedial-btn {
        margin-top: 0.5rem;
    }


    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        /* Điều chỉnh vị trí scroll buttons để không đè lên speedial */
        #scroll-to-top, #scroll-to-bottom {
            bottom: 15rem; /* Tăng khoảng cách để tránh đè lên speedial */
            right: 1rem;
            width: 2.75rem;
            height: 2.75rem;
        }
        #scroll-to-top svg, #scroll-to-bottom svg {
            width: 1.125rem;
            height: 1.125rem;
        }

        /* Speedial container */
        .fixed.bottom-6.right-6 {
            bottom: 1rem;
            right: 1rem;
        }

        /* Speedial buttons */
        .speedial-btn {
            width: 3.25rem !important;
            height: 3.25rem !important;
        }
        .speedial-btn img {
            width: 2.75rem !important;
            height: 2.75rem !important;
        }

        /* Tooltip adjustments */
        .speedial-btn span.absolute {
            right: 3.5rem !important;
            font-size: 0.75rem !important;
            padding: 0.375rem 0.5rem !important;
        }
    }

    @media (max-width: 480px) {
        /* Scroll buttons cho màn hình nhỏ */
        #scroll-to-top, #scroll-to-bottom {
            bottom: 13rem; /* Điều chỉnh cho màn hình nhỏ */
            right: 0.75rem;
            width: 2.5rem;
            height: 2.5rem;
        }
        #scroll-to-top svg, #scroll-to-bottom svg {
            width: 1rem;
            height: 1rem;
        }

        /* Speedial container cho màn hình nhỏ */
        .fixed.bottom-6.right-6 {
            bottom: 0.75rem;
            right: 0.75rem;
        }

        /* Speedial buttons cho màn hình nhỏ */
        .speedial-btn {
            width: 3rem !important;
            height: 3rem !important;
        }
        .speedial-btn img {
            width: 2.5rem !important;
            height: 2.5rem !important;
        }

        /* Tooltip cho màn hình nhỏ */
        .speedial-btn span.absolute {
            right: 3.25rem !important;
            font-size: 0.6875rem !important;
            padding: 0.25rem 0.375rem !important;
        }
    }

    /* Thêm responsive cho màn hình rất nhỏ */
    @media (max-width: 360px) {
        #scroll-to-top, #scroll-to-bottom {
            bottom: 12rem;
            right: 0.5rem;
            width: 2.25rem;
            height: 2.25rem;
        }
        #scroll-to-top svg, #scroll-to-bottom svg {
            width: 0.875rem;
            height: 0.875rem;
        }

        .fixed.bottom-6.right-6 {
            bottom: 0.5rem;
            right: 0.5rem;
        }

        .speedial-btn {
            width: 2.75rem !important;
            height: 2.75rem !important;
        }
        .speedial-btn img {
            width: 2.25rem !important;
            height: 2.25rem !important;
        }

        .speedial-btn span.absolute {
            right: 3rem !important;
            font-size: 0.625rem !important;
            padding: 0.25rem !important;
        }
    }
</style>
<style>
/* Speedial tooltip behavior fix: fully hide when not hovered */
.speedial-btn { position: relative; }
.speedial-btn span.absolute { visibility: hidden; pointer-events: none; opacity: 0; transform: translateX(0.25rem); transition: opacity .2s ease, transform .2s ease, visibility 0s linear .2s; will-change: opacity, transform; }
.speedial-btn:hover span.absolute { visibility: visible; pointer-events: auto; opacity: 1; transform: translateX(0); transition-delay: 0s; }
</style>
