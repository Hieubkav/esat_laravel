{{-- Dynamic StoreFront Component --}}
{{-- Hiển thị các components theo thứ tự và cấu hình từ WebDesign --}}

@php
    $visibleComponents = getVisibleWebDesignComponents();
@endphp

@foreach($visibleComponents as $componentKey => $componentConfig)
    @if($componentKey === 'hero-banner')
        @include('components.storefront.hero-banner')

    @elseif($componentKey === 'about-us')
        <section id="about-us" class="py-12 md:py-16 bg-white">
            @include('components.storefront.about-us')
        </section>

    @elseif($componentKey === 'stats-counter')
        <section id="stats-counter" class="py-12 md:py-16 bg-gray-50">
            @include('components.storefront.stats-counter')
        </section>

    @elseif($componentKey === 'featured-products')
        <section id="featured-products" class="py-12 md:py-16 bg-gray-50">
            @include('components.storefront.featured-products')
        </section>

    @elseif($componentKey === 'services')
        <section id="services" class="py-12 md:py-16 bg-gray-50">
            @include('components.storefront.services')
        </section>

    @elseif($componentKey === 'slogan')
        <section id="slogan" class="py-6 md:py-8">
            @include('components.storefront.slogan')
        </section>

    @elseif($componentKey === 'courses-overview')
        <section id="courses-overview" class="py-12 md:py-16 bg-white">
            @include('components.storefront.courses-overview')
        </section>

    @elseif($componentKey === 'partners')
        <section id="partners" class="py-12 md:py-16 bg-white">
            @include('components.storefront.partners')
        </section>

    @elseif($componentKey === 'blog-posts')
        <section id="blog-posts" class="py-12 md:py-16 bg-gray-50">
            @include('components.storefront.blog-posts')
        </section>
    @endif
@endforeach

{{-- Footer được xử lý riêng trong layout --}}
