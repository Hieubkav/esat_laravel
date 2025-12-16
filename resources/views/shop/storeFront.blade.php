@extends('layouts.shop')

@section('content')
    @foreach($components as $component)
        @php $data = $component->config; @endphp
        @switch($component->type)
            @case('hero_carousel')
                @include('components.home.hero-carousel', ['data' => $data])
                @break

            @case('stats')
                <section class="py-12 md:py-16 bg-gray-50">
                    @include('components.home.stats', ['data' => $data])
                </section>
                @break

            @case('about')
                <section class="py-12 md:py-16 bg-white">
                    @include('components.home.about', ['data' => $data])
                </section>
                @break

            @case('product_categories')
                <section class="py-12 md:py-16 bg-gray-50">
                    @include('components.home.product-categories', ['data' => $data])
                </section>
                @break

            @case('featured_products')
                <section class="py-12 md:py-16 bg-white">
                    @include('components.home.featured-products', [
                        'data' => $data,
                        'products' => $componentData[$component->id]['products'] ?? collect()
                    ])
                </section>
                @break

            @case('slogan')
                <section class="py-6 md:py-8">
                    @include('components.home.slogan', ['data' => $data])
                </section>
                @break

            @case('partners')
                <section class="py-12 md:py-16 bg-white">
                    @include('components.home.partners', [
                        'data' => $data,
                        'partners' => $componentData[$component->id]['partners'] ?? collect()
                    ])
                </section>
                @break

            @case('news')
                <section class="py-12 md:py-16 bg-gray-50">
                    @include('components.home.news', [
                        'data' => $data,
                        'posts' => $componentData[$component->id]['posts'] ?? collect()
                    ])
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
    ::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, var(--color-primary-600, #c53030), var(--color-primary-800, #9b2c2c)); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: linear-gradient(to bottom, var(--color-primary-700, #b91c1c), var(--color-primary-900, #7f1d1d)); }
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
