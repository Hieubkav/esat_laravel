<div class="container mx-auto px-4 relative">
    <!-- Background Elements -->
    <div class="hidden lg:block absolute -top-10 -right-16 w-32 h-32 bg-red-50 rounded-full"></div>
    <div class="hidden lg:block absolute -bottom-10 -left-16 w-32 h-32 bg-red-50 rounded-full"></div>
    
    <!-- FAQ Section Header -->
    <div class="text-center max-w-4xl mx-auto mb-16 relative z-10">
        <span class="inline-block py-1 px-3 text-xs font-semibold bg-red-100 text-red-800 rounded-full tracking-wider">HỎI ĐÁP</span>
        <h2 class="mt-4 text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">Câu hỏi <span class="text-red-700">thường gặp</span></h2>
        <p class="mt-6 text-lg text-gray-600">Các thông tin quan trọng giúp quý khách hiểu rõ hơn về sản phẩm và dịch vụ của Vũ Phúc Baking</p>
    </div>
    
    <!-- FAQ Accordion -->
    <div class="max-w-4xl mx-auto" x-data="{ activeAccordion: null }">
        <!-- FAQ Item 1 -->
        <div class="mb-4 border border-gray-200 rounded-xl overflow-hidden">
            <button 
                @click="activeAccordion = activeAccordion === 1 ? null : 1" 
                class="flex items-center justify-between w-full p-5 text-left bg-white hover:bg-gray-50 transition-colors"
                :class="{ 'bg-gray-50': activeAccordion === 1 }"
            >
                <span class="text-lg font-semibold text-gray-900">Vũ Phúc Baking cung cấp những sản phẩm gì?</span>
                <svg class="w-5 h-5 text-red-700 transform transition-transform" :class="{ 'rotate-180': activeAccordion === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="activeAccordion === 1" x-collapse x-cloak class="px-5 py-4 bg-white border-t border-gray-100">
                <p class="text-gray-600">
                    Vũ Phúc Baking cung cấp đa dạng các sản phẩm nguyên liệu làm bánh và pha chế, bao gồm:
                </p>
                <ul class="mt-2 space-y-1 text-gray-600 list-disc list-inside">
                    <li>Bột làm bánh các loại từ Rich Products Vietnam</li>
                    <li>Whipping cream, kem tươi, topping</li>
                    <li>Chocolate và cacao</li>
                    <li>Bột trộn sẵn và premix</li>
                    <li>Nguyên liệu trang trí bánh</li>
                    <li>Thiết bị và dụng cụ làm bánh chuyên nghiệp</li>
                </ul>
            </div>
        </div>
        
        <!-- FAQ Item 2 -->
        <div class="mb-4 border border-gray-200 rounded-xl overflow-hidden">
            <button 
                @click="activeAccordion = activeAccordion === 2 ? null : 2" 
                class="flex items-center justify-between w-full p-5 text-left bg-white hover:bg-gray-50 transition-colors"
                :class="{ 'bg-gray-50': activeAccordion === 2 }"
            >
                <span class="text-lg font-semibold text-gray-900">Quy trình đặt hàng và giao hàng như thế nào?</span>
                <svg class="w-5 h-5 text-red-700 transform transition-transform" :class="{ 'rotate-180': activeAccordion === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="activeAccordion === 2" x-collapse x-cloak class="px-5 py-4 bg-white border-t border-gray-100">
                <p class="text-gray-600">
                    Vũ Phúc Baking cung cấp nhiều hình thức đặt hàng thuận tiện cho khách hàng:
                </p>
                <ul class="mt-2 space-y-1 text-gray-600 list-disc list-inside mb-3">
                    <li>Đặt hàng trực tiếp trên website</li>
                    <li>Đặt hàng qua điện thoại, Zalo hoặc Facebook</li>
                    <li>Đặt hàng trực tiếp tại cửa hàng</li>
                </ul>
                <p class="text-gray-600">
                    Đối với giao hàng, chúng tôi cam kết:
                </p>
                <ul class="mt-2 space-y-1 text-gray-600 list-disc list-inside">
                    <li>Giao hàng nhanh trong ngày đối với khu vực nội thành</li>
                    <li>Miễn phí giao hàng cho đơn từ 1.000.000đ trở lên</li>
                    <li>Giao hàng toàn quốc thông qua đối tác vận chuyển uy tín</li>
                </ul>
            </div>
        </div>
        
        <!-- FAQ Item 3 -->
        <div class="mb-4 border border-gray-200 rounded-xl overflow-hidden">
            <button 
                @click="activeAccordion = activeAccordion === 3 ? null : 3" 
                class="flex items-center justify-between w-full p-5 text-left bg-white hover:bg-gray-50 transition-colors"
                :class="{ 'bg-gray-50': activeAccordion === 3 }"
            >
                <span class="text-lg font-semibold text-gray-900">Làm thế nào để tham gia khóa đào tạo của Vũ Phúc Baking?</span>
                <svg class="w-5 h-5 text-red-700 transform transition-transform" :class="{ 'rotate-180': activeAccordion === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="activeAccordion === 3" x-collapse x-cloak class="px-5 py-4 bg-white border-t border-gray-100">
                <p class="text-gray-600 mb-3">
                    Vũ Phúc Baking tổ chức các khóa đào tạo ngắn hạn và dài hạn dành cho nhiều đối tượng khách hàng khác nhau. Để tham gia, quý khách có thể:
                </p>
                <ul class="mt-2 space-y-1 text-gray-600 list-disc list-inside">
                    <li>Theo dõi thông báo khóa học mới trên website và fanpage của chúng tôi</li>
                    <li>Đăng ký qua số điện thoại: <span class="text-red-700 font-medium">1800-xxxx-xxx</span></li>
                    <li>Đến trực tiếp trung tâm đào tạo để đăng ký và được tư vấn lộ trình học phù hợp</li>
                </ul>
                <p class="mt-3 text-gray-600">
                    Đặc biệt, khách hàng thường xuyên sẽ được ưu đãi học phí và tham gia các buổi workshop đặc biệt.
                </p>
            </div>
        </div>
        
        <!-- FAQ Item 4 -->
        <div class="mb-4 border border-gray-200 rounded-xl overflow-hidden">
            <button 
                @click="activeAccordion = activeAccordion === 4 ? null : 4" 
                class="flex items-center justify-between w-full p-5 text-left bg-white hover:bg-gray-50 transition-colors"
                :class="{ 'bg-gray-50': activeAccordion === 4 }"
            >
                <span class="text-lg font-semibold text-gray-900">Làm thế nào để được hỗ trợ kỹ thuật?</span>
                <svg class="w-5 h-5 text-red-700 transform transition-transform" :class="{ 'rotate-180': activeAccordion === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="activeAccordion === 4" x-collapse x-cloak class="px-5 py-4 bg-white border-t border-gray-100">
                <p class="text-gray-600">
                    Vũ Phúc Baking cam kết hỗ trợ kỹ thuật trọn đời cho khách hàng. Quý khách có thể nhận được hỗ trợ kỹ thuật qua các kênh:
                </p>
                <ul class="mt-2 space-y-1 text-gray-600 list-disc list-inside mb-3">
                    <li>Đường dây nóng hỗ trợ kỹ thuật: <span class="text-red-700 font-medium">1800-xxxx-xxx</span></li>
                    <li>Email hỗ trợ: <span class="text-red-700 font-medium">hotro@vuphucbaking.vn</span></li>
                    <li>Chat trực tiếp với chuyên gia trên website</li>
                    <li>Đặt lịch tư vấn trực tiếp tại cơ sở sản xuất của quý khách</li>
                </ul>
                <p class="text-gray-600">
                    Đội ngũ chuyên gia của chúng tôi sẽ hỗ trợ giải đáp mọi thắc mắc và đưa ra giải pháp phù hợp nhất cho quý khách.
                </p>
            </div>
        </div>
        
        <!-- FAQ Item 5 -->
        <div class="mb-4 border border-gray-200 rounded-xl overflow-hidden">
            <button 
                @click="activeAccordion = activeAccordion === 5 ? null : 5" 
                class="flex items-center justify-between w-full p-5 text-left bg-white hover:bg-gray-50 transition-colors"
                :class="{ 'bg-gray-50': activeAccordion === 5 }"
            >
                <span class="text-lg font-semibold text-gray-900">Sản phẩm của Vũ Phúc Baking có bảo hành không?</span>
                <svg class="w-5 h-5 text-red-700 transform transition-transform" :class="{ 'rotate-180': activeAccordion === 5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="activeAccordion === 5" x-collapse x-cloak class="px-5 py-4 bg-white border-t border-gray-100">
                <p class="text-gray-600">
                    Các sản phẩm của Vũ Phúc Baking đều được đảm bảo chất lượng và có chính sách bảo hành rõ ràng:
                </p>
                <ul class="mt-2 space-y-1 text-gray-600 list-disc list-inside">
                    <li>Đối với thiết bị, dụng cụ làm bánh: Bảo hành từ 6-12 tháng tùy loại sản phẩm</li>
                    <li>Đối với nguyên liệu: Đảm bảo chất lượng trong thời hạn sử dụng ghi trên bao bì</li>
                    <li>Chính sách đổi trả: Có thể đổi trả trong vòng 7 ngày nếu sản phẩm có vấn đề về chất lượng</li>
                </ul>
                <p class="mt-3 text-gray-600">
                    Ngoài ra, quý khách luôn được hỗ trợ kỹ thuật miễn phí khi sử dụng các sản phẩm của Vũ Phúc Baking.
                </p>
            </div>
        </div>
        
        <!-- FAQ Item 6 -->
        <div class="mb-4 border border-gray-200 rounded-xl overflow-hidden">
            <button 
                @click="activeAccordion = activeAccordion === 6 ? null : 6" 
                class="flex items-center justify-between w-full p-5 text-left bg-white hover:bg-gray-50 transition-colors"
                :class="{ 'bg-gray-50': activeAccordion === 6 }"
            >
                <span class="text-lg font-semibold text-gray-900">Làm thế nào để trở thành đại lý của Vũ Phúc Baking?</span>
                <svg class="w-5 h-5 text-red-700 transform transition-transform" :class="{ 'rotate-180': activeAccordion === 6 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="activeAccordion === 6" x-collapse x-cloak class="px-5 py-4 bg-white border-t border-gray-100">
                <p class="text-gray-600 mb-3">
                    Vũ Phúc Baking luôn chào đón các đối tác muốn trở thành đại lý phân phối sản phẩm. Quy trình đăng ký:
                </p>
                <ul class="mt-2 space-y-1 text-gray-600 list-disc list-inside mb-3">
                    <li>Liên hệ với bộ phận phát triển đại lý: <span class="text-red-700 font-medium">daily@vuphucbaking.vn</span></li>
                    <li>Điền đơn đăng ký và cung cấp thông tin kinh doanh</li>
                    <li>Tham gia buổi tư vấn và tìm hiểu chính sách đại lý</li>
                    <li>Ký kết hợp đồng hợp tác</li>
                </ul>
                <p class="text-gray-600">
                    Đại lý của Vũ Phúc Baking sẽ được hưởng nhiều ưu đãi về giá, chính sách thanh toán linh hoạt và được hỗ trợ đào tạo chuyên nghiệp.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Extra Support Section -->
    <div class="mt-16 max-w-4xl mx-auto flex flex-col md:flex-row items-center justify-between bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-8 gap-8">
        <div class="text-center md:text-left">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Không tìm thấy câu trả lời?</h3>
            <p class="text-gray-600">Liên hệ với đội ngũ chăm sóc khách hàng của chúng tôi để được hỗ trợ nhanh chóng.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="#" class="inline-flex items-center justify-center px-6 py-3 bg-red-700 text-white font-medium rounded-lg hover:bg-red-800 transition-colors shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Gọi ngay
            </a>
            <a href="#" class="inline-flex items-center justify-center px-6 py-3 border-2 border-red-700 text-red-700 font-medium rounded-lg hover:bg-red-700 hover:text-white transition-all shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                Chat ngay
            </a>
        </div>
    </div>
</div>