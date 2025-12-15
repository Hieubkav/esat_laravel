<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SEO Meta Tags -->
    <title>@yield('title', ($settings && $settings->seo_title) ? $settings->seo_title : (($settings && $settings->site_name) ? $settings->site_name : config('app.name')))</title>
    <meta name="description" content="@yield('description', 'ESAT - Chuyên cung cấp thiết bị điện tử chất lượng cao')">
    <meta name="keywords" content="ESAT, thiết bị điện tử, linh kiện điện tử, thiết bị công nghệ">
    <meta name="robots" content="all">
    <meta name="theme-color" content="#b91c1c">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', ($settings && $settings->site_name) ? $settings->site_name : 'ESAT')">
    <meta property="og:description" content="@yield('description', 'Chuyên cung cấp thiết bị điện tử chất lượng cao')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ ($settings && $settings->og_image_link) ? asset('storage/' . $settings->og_image_link) : (($settings && $settings->logo_link) ? asset('storage/' . $settings->logo_link) : \App\Helpers\PlaceholderHelper::getLogo()) }}">

    <!-- Structured Data -->
    @if(isset($seoData['structuredData']))
        <script type="application/ld+json">{!! json_encode($seoData['structuredData'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
    @if(isset($seoData['breadcrumbs']))
        <script type="application/ld+json">{!! json_encode(\App\Services\SeoService::getBreadcrumbStructuredData($seoData['breadcrumbs']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    <!-- External Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Favicon -->
    @php $favicon = ($settings && $settings->favicon_link) ? asset('storage/' . $settings->favicon_link) : (($settings && $settings->logo_link) ? asset('storage/' . $settings->logo_link) : \App\Helpers\PlaceholderHelper::getLogo()); @endphp
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $favicon }}">

    <!-- Core Styles -->
    <style>
        /* CSS Variables */
        :root { --primary: #b91c1c; --primary-dark: #991b1b; }

        /* Base Styles */
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Open Sans', system-ui, sans-serif; color: #374151; }
        h1, h2, h3, h4, h5, h6, .heading { font-family: 'Montserrat', system-ui, sans-serif; }

        /* Utilities */
        .text-primary { color: var(--primary); }
        .bg-primary { background-color: var(--primary); }
        .page-container { max-width: 1440px; margin: 0 auto; }

        /* Animations */
        .section-transition { transition: all 0.5s ease-in-out; }
        .section-transition:hover { transform: translateY(-5px); }

        /* Product Cards */
        .product-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .product-image { transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .product-card:hover .product-image { transform: scale(1.1); }

        /* Line Clamp */
        .line-clamp-2 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .line-clamp-3 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }

        /* Prose */
        .prose { color: #374151; max-width: none; }
        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 { color: #111827; font-weight: 600; font-family: 'Montserrat', system-ui, sans-serif; }
        .prose-red a { color: var(--primary); }
        .prose-red a:hover { color: var(--primary-dark); }

        /* Pagination */
        .pagination { display: flex; justify-content: center; align-items: center; gap: 0.5rem; }
        .pagination .page-link { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; color: #374151; text-decoration: none; transition: all 0.2s; }
        .pagination .page-link:hover { background-color: #f3f4f6; border-color: var(--primary); color: var(--primary); }
        .pagination .page-link.active { background-color: var(--primary); border-color: var(--primary); color: white; }



        /* Mobile Filter */
        @media (max-width: 1023px) {
            .filter-sidebar { position: fixed; top: 0; left: -100%; width: 100%; height: 100vh; background: white; z-index: 50; transition: left 0.3s ease; overflow-y: auto; }
            .filter-sidebar.active { left: 0; }
            .filter-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0, 0, 0, 0.5); z-index: 40; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
            .filter-overlay.active { opacity: 1; visibility: visible; }
        }
    </style>

    @filamentStyles
    @vite(['resources/css/app.css', 'resources/css/auth-modal.css'])
    <link rel="stylesheet" href="{{ asset('css/image-responsive.css') }}">
    @stack('styles')
</head>

<body class="antialiased min-h-screen flex flex-col">
    <!-- Preloader -->
    <div id="page-preloader" class="fixed inset-0 bg-white z-50 flex items-center justify-center transition-opacity duration-500">
        <div class="flex flex-col items-center">
            @if($settings && $settings->logo_link)
                <img src="{{ asset('storage/' . $settings->logo_link) }}" alt="{{ ($settings && $settings->site_name) ? $settings->site_name : config('app.name') }}" class="h-18 w-auto mb-4 animate-pulse" loading="eager">
            @else
                <div class="h-18 w-18 mb-4 bg-red-600 rounded-lg flex items-center justify-center animate-pulse">
                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5z"/></svg>
                </div>
            @endif
            <div class="w-32 h-1 bg-gradient-to-r from-red-700 to-red-500 rounded-full animate-pulse"></div>
            <p class="text-sm text-gray-600 mt-2 animate-pulse">{{ ($settings && $settings->site_name) ? $settings->site_name : 'Đang tải...' }}</p>
        </div>
    </div>

    <!-- Navigation -->
    {{-- @include('components.public.subnav') --}}
    @include('components.public.navbar')

    <!-- Main Content -->
    <main class="flex-grow bg-gray-50 overflow-visible">
        @yield('content')
    </main>

    <!-- Global CTA -->
    <section class="py-12 md:py-16 bg-gradient-to-r from-red-800 via-red-700 to-red-600 text-white relative overflow-hidden">
        @include('components.storefront.homepage-cta')
    </section>

    <!-- Footer & Components -->
    @if(webDesignVisible('footer'))
    <footer id="footer">
        @include('components.public.footer')
    </footer>
    @endif
    @include('components.public.speedial')
    @livewire('notifications')

    <!-- Scripts -->
    @filamentScripts
    @if(app()->environment('production'))
        @vite(['resources/js/fallback-detection.js', 'resources/js/performance-optimizer.js', 'resources/js/app.js', 'resources/js/csrf-helper.js', 'resources/js/auth-state-manager.js', 'resources/js/ajax-handlers.js'])
    @else
        @vite(['resources/js/performance-optimizer.js', 'resources/js/app.js', 'resources/js/csrf-helper.js', 'resources/js/auth-state-manager.js', 'resources/js/ajax-handlers.js'])
    @endif
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/image-smart.js') }}"></script>
    @stack('scripts')



    <!-- Core JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hide preloader
            const preloader = document.getElementById('page-preloader');
            if (preloader) {
                setTimeout(() => { preloader.style.opacity = 0; setTimeout(() => preloader.style.display = 'none', 500); }, 500);
            }


        });
    </script>
</body>
</html>
