@php
    use App\Models\DeliveryRoute;
    $deliveryRoutes = DeliveryRoute::where('status', 1)->orderBy('order')->get();
    $firstRoute = $deliveryRoutes->first();
@endphp

<section class="py-20 bg-white" id="delivery-routes">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Tuyến giao hàng</h2>
            <div class="w-24 h-1 bg-red-600 mx-auto mt-4"></div>
        </div>

        <!-- Map and Description -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            <div class="relative h-96 lg:h-auto order-2 lg:order-1">
                <div id="main-route-image" class="w-full h-full">
                    @if($firstRoute?->image)
                        <img 
                            src="{{ asset('storage/' . $firstRoute->image) }}" 
                            alt="{{ $firstRoute->name }}"
                            class="rounded-lg w-full h-full object-cover transition-opacity duration-300"
                        >
                    @else
                        <div class="w-full h-full bg-gray-50 flex items-center justify-center rounded-lg border border-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="flex flex-col justify-center order-1 lg:order-2">
                <h3 class="text-2xl font-medium text-gray-900 mb-6">Giao hàng nhanh chóng & tin cậy</h3>
                <p class="text-gray-600 mb-8">
                    Vũ Phúc Baking cung cấp dịch vụ giao hàng chuyên nghiệp đến các tỉnh thành Đồng bằng sông Cửu Long, đảm bảo sản phẩm đến tay khách hàng nhanh chóng và an toàn.
                </p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 mb-8">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-red-50 flex items-center justify-center mr-3 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-gray-700">Giao hàng đúng hẹn</span>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-red-50 flex items-center justify-center mr-3 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-gray-700">Đóng gói cẩn thận</span>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-red-50 flex items-center justify-center mr-3 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-gray-700">Nhân viên thân thiện</span>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-red-50 flex items-center justify-center mr-3 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="text-gray-700">Theo dõi đơn hàng dễ dàng</span>
                    </div>
                </div>
                
                <a href="#" class="inline-flex items-center text-red-600 hover:text-red-700 transition-colors font-medium">
                    <span>Liên hệ đặt hàng</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Delivery Routes Grid/Swiper -->
        @if($deliveryRoutes->count() > 0)
            <div class="mt-8">
                <h3 class="text-xl font-medium text-center mb-8">Các tuyến giao hàng định kỳ</h3>
                
                <!-- Desktop Grid View (hidden on mobile) -->
                <div class="hidden md:grid md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($deliveryRoutes as $route)
                        <div 
                            class="bg-white border border-gray-100 hover:border-red-100 hover:shadow-sm transition-all rounded p-6 cursor-pointer route-card"
                            data-route-id="{{ $route->id }}"
                            data-route-name="{{ $route->name }}"
                            @if($route->image) data-route-image="{{ asset('storage/' . $route->image) }}" @endif
                        >
                            @if($route->image)
                                <div class="mb-4 h-40 overflow-hidden rounded">
                                    <img 
                                        src="{{ asset('storage/' . $route->image) }}" 
                                        alt="{{ $route->name }}" 
                                        class="w-full h-full object-cover"
                                    >
                                </div>
                            @endif
                            <h4 class="font-medium text-gray-900 mb-2">{{ $route->name }}</h4>
                            @if($route->description)
                                <p class="text-sm text-gray-600">{{ $route->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <!-- Mobile Swiper (visible only on mobile) -->
                <div class="block md:hidden">
                    <!-- Swiper -->
                    <div class="swiper mobile-route-swiper">
                        <div class="swiper-wrapper">
                            @foreach($deliveryRoutes as $route)
                                <div class="swiper-slide">
                                    <div 
                                        class="bg-white border border-gray-100 rounded p-6 cursor-pointer route-card h-full"
                                        data-route-id="{{ $route->id }}"
                                        data-route-name="{{ $route->name }}"
                                        @if($route->image) data-route-image="{{ asset('storage/' . $route->image) }}" @endif
                                    >
                                        @if($route->image)
                                            <div class="mb-4 h-40 overflow-hidden rounded">
                                                <img 
                                                    src="{{ asset('storage/' . $route->image) }}" 
                                                    alt="{{ $route->name }}" 
                                                    class="w-full h-full object-cover"
                                                >
                                            </div>
                                        @endif
                                        <h4 class="font-medium text-gray-900 mb-2">{{ $route->name }}</h4>
                                        @if($route->description)
                                            <p class="text-sm text-gray-600">{{ $route->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Add Pagination -->
                        <div class="swiper-pagination"></div>
                        
                        <!-- Navigation buttons -->
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<style>
    .mobile-route-swiper {
        width: 100%;
        padding-bottom: 45px;
        padding-left: 5px;
        padding-right: 5px;
    }
    .mobile-route-swiper .swiper-pagination {
        bottom: 0px;
    }
    .mobile-route-swiper .swiper-pagination-bullet-active {
        background-color: #dc2626;
    }
    .mobile-route-swiper .swiper-button-next,
    .mobile-route-swiper .swiper-button-prev {
        color: #dc2626;
        --swiper-navigation-size: 25px;
    }
    .mobile-route-swiper .swiper-button-next:after,
    .mobile-route-swiper .swiper-button-prev:after {
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Swiper for mobile
        const mobileSwiper = new Swiper('.mobile-route-swiper', {
            slidesPerView: 1.1,
            spaceBetween: 15,
            centeredSlides: false,
            grabCursor: true,
            loop: false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                480: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
            }
        });
        
        // Attach click events to route cards (both desktop and mobile)
        const allRouteCards = document.querySelectorAll('.route-card');
        allRouteCards.forEach(card => {
            card.addEventListener('click', updateMainImage);
        });
        
        function updateMainImage() {
            const mainRouteImage = document.getElementById('main-route-image');
            // Lấy thông tin của tuyến được nhấp vào
            const routeImage = this.dataset.routeImage;
            const routeName = this.dataset.routeName;
            
            if (routeImage && mainRouteImage) {
                // Tạo hiệu ứng chuyển đổi mềm mại
                const oldImage = mainRouteImage.querySelector('img');
                if (oldImage) {
                    // Tạo hiệu ứng fade out
                    oldImage.style.opacity = '0';
                    
                    setTimeout(() => {
                        // Cập nhật ảnh sau khi fade out
                        oldImage.src = routeImage;
                        oldImage.alt = routeName;
                        
                        // Tạo hiệu ứng fade in
                        setTimeout(() => {
                            oldImage.style.opacity = '1';
                        }, 50);
                    }, 300);
                } else {
                    // Nếu không có ảnh sẵn, tạo mới
                    mainRouteImage.innerHTML = `
                        <img 
                            src="${routeImage}" 
                            alt="${routeName}"
                            class="rounded-lg w-full h-full object-cover transition-opacity duration-300"
                            style="opacity: 0;"
                        >
                    `;
                    
                    // Tạo hiệu ứng fade in sau khi đã tạo ảnh
                    setTimeout(() => {
                        const newImage = mainRouteImage.querySelector('img');
                        if (newImage) {
                            newImage.style.opacity = '1';
                        }
                    }, 50);
                }
                
                // Highlight card được chọn
                allRouteCards.forEach(c => {
                    c.classList.remove('border-red-300', 'bg-red-50');
                });
                this.classList.add('border-red-300', 'bg-red-50');
                
                // Cuộn lên trên để hiển thị ảnh chính nếu đang ở mobile
                if (window.innerWidth < 768) {
                    const deliveryRoutesSection = document.getElementById('delivery-routes');
                    if (deliveryRoutesSection) {
                        deliveryRoutesSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            }
        }
    });
</script>
@endpush