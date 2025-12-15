@extends('layouts.shop')

@section('title', 'Tất cả sản phẩm - Vũ Phúc Baking')
@section('description', 'Khám phá bộ sưu tập đầy đủ các sản phẩm nguyên liệu làm bánh, dụng cụ và thiết bị chuyên nghiệp tại Vũ Phúc Baking. Chất lượng cao, giá cả hợp lý.')

@push('meta')
<meta property="og:title" content="Tất cả sản phẩm - Vũ Phúc Baking">
<meta property="og:description" content="Khám phá bộ sưu tập đầy đủ các sản phẩm nguyên liệu làm bánh, dụng cụ và thiết bị chuyên nghiệp tại Vũ Phúc Baking. Chất lượng cao, giá cả hợp lý.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ route('products.categories') }}">
<meta property="og:image" content="{{ App\Services\SeoService::getDefaultOgImage() }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Tất cả sản phẩm - Vũ Phúc Baking">
<meta name="twitter:description" content="Khám phá bộ sưu tập đầy đủ các sản phẩm nguyên liệu làm bánh, dụng cụ và thiết bị chuyên nghiệp tại Vũ Phúc Baking. Chất lượng cao, giá cả hợp lý.">
<meta name="twitter:image" content="{{ App\Services\SeoService::getDefaultOgImage() }}">
@endpush

@push('styles')
<style>
    .filter-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .filter-btn {
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        background-color: #fef2f2;
        color: #dc2626;
    }

    .filter-btn.active {
        background-color: #dc2626;
        color: white;
    }

    .product-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover {
        transform: translateY(-4px);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Mobile filter sidebar */
    .mobile-filter-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 320px;
        height: 100vh;
        z-index: 50;
        transition: left 0.3s ease;
        overflow-y: auto;
        background: white;
    }

    .mobile-filter-sidebar.active {
        left: 0;
    }

    .mobile-filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 40;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .mobile-filter-overlay.active {
        opacity: 1;
        visibility: visible;
    }
</style>
@endpush

@section('content')
    @livewire('products-filter')

    <!-- Mobile Filter Sidebar -->
    <div id="mobile-filter-overlay" class="mobile-filter-overlay lg:hidden"></div>
    <div id="mobile-filter-sidebar" class="mobile-filter-sidebar lg:hidden">
        <div class="p-6">
            <!-- Mobile Close Button -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 font-montserrat">Bộ lọc sản phẩm</h2>
                <button id="mobile-filter-close" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>

            <!-- Mobile Filter Content (will be populated by Livewire) -->
            <div id="mobile-filter-content">
                <!-- This will be populated by JavaScript from desktop filters -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileFilterOverlay = document.getElementById('mobile-filter-overlay');
        const mobileFilterSidebar = document.getElementById('mobile-filter-sidebar');
        const mobileFilterClose = document.getElementById('mobile-filter-close');
        const mobileFilterContent = document.getElementById('mobile-filter-content');
        const desktopFilterContent = document.getElementById('desktop-filter-content');

        function openMobileFilter() {
            // Copy desktop filter content to mobile
            if (desktopFilterContent && mobileFilterContent) {
                mobileFilterContent.innerHTML = desktopFilterContent.innerHTML;
            }

            mobileFilterSidebar.classList.add('active');
            mobileFilterOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileFilter() {
            mobileFilterSidebar.classList.remove('active');
            mobileFilterOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Listen for Livewire event
        document.addEventListener('toggle-mobile-filter', openMobileFilter);

        if (mobileFilterClose) {
            mobileFilterClose.addEventListener('click', closeMobileFilter);
        }

        if (mobileFilterOverlay) {
            mobileFilterOverlay.addEventListener('click', closeMobileFilter);
        }

        // Close filter on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileFilter();
            }
        });

        // Handle clicks on mobile filter buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('#mobile-filter-content .filter-btn')) {
                // Close mobile filter after selection
                setTimeout(closeMobileFilter, 100);
            }
        });
    });
</script>
@endpush
