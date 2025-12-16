@extends('layouts.shop')

@section('title', isset($selectedCategory) ? $selectedCategory->name . ' - Bài viết' : 'Bài viết - ESAT')
@section('description', isset($selectedCategory) ? ($selectedCategory->description ?: 'Chuyên mục ' . $selectedCategory->name) : 'Khám phá tất cả bài viết, tin tức tại ESAT')

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
        background-color: var(--color-primary-50);
        color: var(--color-primary-600);
    }

    .filter-btn.active {
        background-color: var(--color-primary-600);
        color: white;
    }

    .post-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .post-card:hover {
        transform: translateY(-4px);
    }

    /* Cnh ?nh b…i vi?t c ng t? l?, cng chi?u cao */
    .post-card .post-thumb {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        background: #f8fafc;
        overflow: hidden;
    }

    .post-card .post-thumb img,
    .post-card .post-thumb .image-placeholder {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
    @livewire('posts-filter', ['selectedCategory' => $selectedCategory ?? null])

    <!-- Mobile Filter Sidebar -->
    <div id="mobile-filter-overlay" class="mobile-filter-overlay lg:hidden"></div>
    <div id="mobile-filter-sidebar" class="mobile-filter-sidebar lg:hidden">
        <div class="p-6">
            <!-- Mobile Close Button -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 font-montserrat">Bộ lọc bài viết</h2>
                <button id="mobile-filter-close" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>

            <!-- Mobile Filter Content -->
            <div id="mobile-filter-content"></div>
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

        document.addEventListener('toggle-mobile-filter', openMobileFilter);

        if (mobileFilterClose) {
            mobileFilterClose.addEventListener('click', closeMobileFilter);
        }

        if (mobileFilterOverlay) {
            mobileFilterOverlay.addEventListener('click', closeMobileFilter);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileFilter();
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('#mobile-filter-content .filter-btn')) {
                setTimeout(closeMobileFilter, 100);
            }
        });
    });
</script>
@endpush
