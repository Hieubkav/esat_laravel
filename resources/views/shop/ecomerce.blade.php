@extends('layouts.shop')

@section('content')
    <!-- Hero Banner cho trang mua hàng -->
    @include('components.ecomerce.hero-banner')
    
    <!-- Danh mục sản phẩm -->
    <section class="py-16 md:py-24">
        @include('components.storefront.categories')
    </section>

    <!-- Sản phẩm nổi bật -->
    <section id="featured-products" class="py-16 md:py-24 bg-gray-50">
        @include('components.storefront.featured-products')
    </section>

    <!-- Bộ sưu tập sản phẩm đặc biệt -->
    <section class="py-16 md:py-24 bg-white">
        @include('components.storefront.product-gallery')
    </section>

    <!-- Dịch vụ và tiện ích mua hàng -->
    <section class="py-16 md:py-24 bg-gray-50">
        @include('components.ecomerce.shopping-services')
    </section>
    
    <!-- Lộ trình giao hàng -->
    <section class="animate-on-scroll py-16 md:py-24 bg-white">
        @include('components.storefront.delivery-routes')
    </section>
    
    <!-- Đánh giá của khách hàng -->
    <section class="animate-on-scroll py-16 md:py-24 bg-gray-50">
        @include('components.storefront.testimonials')
    </section>
    
    <!-- Câu hỏi thường gặp về mua hàng -->
    <section class="animate-on-scroll py-16 md:py-24 bg-white">
        @include('components.ecomerce.shopping-faq')
    </section>
@endsection

@push('styles')
<style>
    /* Premium styling and animations */
    section {
        position: relative;
        transition: all 0.5s ease;
    }
    

    

    
    /* Luxury scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f8f8f8;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #c53030, #9b2c2c);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #b91c1c, #7f1d1d);
    }
    
    /* Subtle background pattern */
    .bg-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.15'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
    }
    
    /* Expand animation for accordion FAQ */
    details[open] summary ~ * {
        animation: slideDown 0.3s ease-in-out;
    }
    
    details:not([open]) summary ~ * {
        animation: slideUp 0.3s ease-in-out;
    }
    
    @keyframes slideDown {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideUp {
        0% {
            opacity: 1;
            transform: translateY(0);
        }
        100% {
            opacity: 0;
            transform: translateY(-10px);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Subtle parallax effect for premium feel
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.parallax-bg');

            parallaxElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                element.style.transform = `translateY(${scrollTop * speed}px)`;
            });
        });
    });
</script>
@endpush