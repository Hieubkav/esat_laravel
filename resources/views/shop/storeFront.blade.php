@extends('layouts.shop')

@section('content')
    @foreach($components as $component)
        @switch($component->type)
            @case('hero_carousel')
                @include('components.home.hero-carousel', ['data' => $component->config])
                @break

            @case('stats')
                <section class="py-12 md:py-16 bg-gray-50">
                    @include('components.home.stats', ['data' => $component->config])
                </section>
                @break

            @case('about')
                <section class="py-12 md:py-16 bg-white">
                    @include('components.home.about', ['data' => $component->config])
                </section>
                @break

            @case('product_categories')
                <section class="py-12 md:py-16 bg-gray-50">
                    @include('components.home.product-categories', ['data' => $component->config])
                </section>
                @break

            @case('featured_products')
                <section class="py-12 md:py-16 bg-white">
                    @include('components.home.featured-products', ['data' => $component->config])
                </section>
                @break

            @case('slogan')
                <section class="py-6 md:py-8">
                    @include('components.home.slogan', ['data' => $component->config])
                </section>
                @break

            @case('partners')
                <section class="py-12 md:py-16 bg-white">
                    @include('components.home.partners', ['data' => $component->config])
                </section>
                @break

            @case('news')
                <section class="py-12 md:py-16 bg-gray-50">
                    @include('components.home.news', ['data' => $component->config])
                </section>
                @break
        @endswitch
    @endforeach
@endsection

@push('styles')
<style>
    /* Custom scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f8f8f8; }
    ::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, #c53030, #9b2c2c); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: linear-gradient(to bottom, #b91c1c, #7f1d1d); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Parallax effect
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset;
        document.querySelectorAll('.parallax-bg').forEach(element => {
            const speed = element.dataset.speed || 0.5;
            element.style.transform = `translateY(${scrollTop * speed}px)`;
        });
    });
});
</script>
@endpush
