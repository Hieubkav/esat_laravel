<section class="relative bg-gradient-to-r from-red-700 to-red-800 text-white py-16 md:py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-pattern"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-12">
            <div class="lg:w-1/2 text-center lg:text-left">
                <span class="inline-block px-4 py-1 bg-white bg-opacity-20 rounded-full text-sm font-semibold mb-4">BÁNH TƯƠI MỖI NGÀY</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">Khám Phá Bộ Sưu Tập <span class="italic">Bánh Ngọt</span> Tươi Ngon</h1>
                <p class="text-white text-opacity-90 text-lg mb-8 max-w-xl mx-auto lg:mx-0">Từ bánh mì Pháp truyền thống đến bánh ngọt hiện đại, chúng tôi mang đến những sản phẩm chất lượng cao, được làm thủ công mỗi ngày.</p>
                <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                    <a href="#featured-products" class="px-6 py-3 bg-white text-red-700 font-semibold rounded-lg hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Xem sản phẩm
                    </a>
                    <a href="{{ route('products.categories') }}" class="px-6 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-red-700 transition-all hover:shadow-lg">
                        Danh mục bánh
                    </a>
                </div>
            </div>
            <div class="lg:w-1/2 relative">
                <div class="rounded-lg overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&h=600&q=80" alt="Các loại bánh tại Vũ Phúc Baking" class="w-full h-auto">
                </div>
                <div class="absolute -bottom-4 -right-4 bg-white rounded-lg shadow-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-red-600 rounded-full p-2 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-800 font-semibold">Miễn phí giao hàng</p>
                            <p class="text-gray-500 text-sm">Cho đơn hàng từ 500.000đ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>