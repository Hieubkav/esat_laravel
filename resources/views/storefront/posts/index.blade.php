@extends('layouts.shop')

@section('title', 'Bài viết - Vũ Phúc Baking')
@section('description', 'Khám phá tất cả bài viết, tin tức, khóa học và dịch vụ chất lượng cao tại Vũ Phúc Baking')

@push('styles')
<style>
    .hero-pattern {
        background-image:
            radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 2px, transparent 2px),
            radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 2px, transparent 2px);
        background-size: 50px 50px;
    }

    .filter-card {
        background: white;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease;
    }

    .filter-card:hover {
        border-color: rgba(220, 38, 38, 0.2);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .post-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .post-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .filter-btn {
        transition: all 0.2s ease;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .filter-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #dc2626;
    }

    .filter-btn.active {
        background: #dc2626;
        color: white;
        border-color: #dc2626;
        box-shadow: 0 1px 3px 0 rgba(220, 38, 38, 0.3);
    }

    .mobile-sidebar {
        display: none;
    }

    .mobile-sidebar.active {
        display: block;
        position: fixed;
        inset: 0;
        z-index: 50;
        background: rgba(0, 0, 0, 0.5);
    }

    .mobile-sidebar-content {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 320px;
        background: white;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        overflow-y: auto;
    }

    .mobile-sidebar.active .mobile-sidebar-content {
        transform: translateX(0);
    }

    @media (min-width: 1024px) {
        .mobile-sidebar {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<section class="pt-24 pb-16 bg-gray-50">
    <div class="container mx-auto px-4">
        @livewire('posts-filter')
    </div>
</section>
@endsection