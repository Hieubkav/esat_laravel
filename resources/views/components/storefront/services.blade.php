@php
    // Lấy dữ liệu từ WebDesign
    $servicesWebDesign = webDesignData('services');
    $isVisible = webDesignVisible('services');

    // Lấy 3 bài viết service mới nhất
    $servicesData = collect();
    if ($isVisible) {
        try {
            $servicesData = \App\Models\Post::where('status', 'active')
                ->where('type', 'service')
                ->with(['categories', 'images'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        } catch (\Exception $e) {
            $servicesData = collect();
        }
    }
@endphp

{{-- Chỉ hiển thị section nếu được bật và có dữ liệu services --}}
@if($isVisible && $servicesData->count() > 0)
<div class="container mx-auto px-4">
    <!-- Section Header -->
    <div class="text-center mb-10 md:mb-12">
        {{-- <span class="section-badge">DỊCH VỤ</span> --}}
        <h2 class="section-title mt-4">
            {{ $servicesWebDesign?->title ?? 'Dịch vụ chuyên nghiệp' }}
        </h2>
        <p class="section-subtitle mt-5">
            {{ $servicesWebDesign?->subtitle ?? 'Chúng tôi cung cấp đa dạng dịch vụ chất lượng cao để hỗ trợ khách hàng phát triển bền vững' }}
        </p>
    </div>

    <!-- Services Grid - Responsive layout tự động điều chỉnh theo số lượng -->
    @php
        $gridClasses = 'grid gap-6 md:gap-8 mx-auto';
        $count = $servicesData->count();

        if ($count == 1) {
            $gridClasses .= ' grid-cols-1 max-w-md';
        } elseif ($count == 2) {
            $gridClasses .= ' grid-cols-1 md:grid-cols-2 max-w-4xl';
        } elseif ($count == 3) {
            $gridClasses .= ' grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-w-5xl';
        } elseif ($count == 4) {
            $gridClasses .= ' grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 max-w-6xl';
        } else {
            $gridClasses .= ' grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-w-7xl';
        }
    @endphp
    <div class="{{ $gridClasses }}">

        @foreach($servicesData as $service)
            <div class="group bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer">
                <!-- Service Image -->
                <div class="h-48 relative overflow-hidden">
                    @if(isset($service->thumbnail) && !empty($service->thumbnail))
                        <img src="{{ asset('storage/' . $service->thumbnail) }}"
                             alt="{{ $service->title ?? 'Dịch vụ' }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @else
                        <!-- Placeholder với icon bánh tự tạo -->
                        <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 flex flex-col items-center justify-center relative overflow-hidden">
                            <!-- Background pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-4 left-4 w-3 h-3 bg-red-200 rounded-full"></div>
                                <div class="absolute top-8 right-6 w-2 h-2 bg-red-200 rounded-full"></div>
                                <div class="absolute bottom-6 left-8 w-2 h-2 bg-red-200 rounded-full"></div>
                                <div class="absolute bottom-4 right-4 w-3 h-3 bg-red-200 rounded-full"></div>
                            </div>

                            <!-- Main cake icon -->
                            <div class="relative z-10 flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.5 1.5 0 013 15.546V12a9 9 0 0118 0v3.546z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16.5V21m0-4.5c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3s-4.5 4.03-4.5 9 2.015 9 4.5 9z" />
                                </svg>
                                <span class="text-xs text-red-400 font-medium">{{ Str::limit($service->title ?? 'Dịch vụ', 20) }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>

                <!-- Service Content -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3 font-montserrat group-hover:text-red-700 transition-colors duration-300">
                        {{ $service->title ?? 'Dịch vụ' }}
                    </h3>

                    @if(isset($service->content) && !empty($service->content))
                        <p class="text-gray-600 mb-4 font-open-sans line-clamp-3">
                            {{ Str::limit(strip_tags($service->content), 120) }}
                        </p>
                    @endif

                    <!-- Action Link -->
                    <div class="pt-2 border-t border-gray-100">
                        <a href="{{ isset($service->slug) ? route('posts.show', $service->slug) : '#' }}"
                           class="inline-flex items-center text-red-700 font-medium hover:text-red-800 transition-colors font-open-sans">
                            <span>Tìm hiểu thêm</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- View All Services Button -->
    <div class="mt-10 text-center">
        <a href="{{ route('posts.services') }}"
           class="group inline-flex items-center justify-center px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <span class="font-montserrat">Xem tất cả dịch vụ</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</div>
@endif