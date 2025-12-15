<div class="container mx-auto px-4 relative">
    <!-- Background Decoration -->
    <div class="absolute -top-20 -left-20 w-64 h-64 bg-red-50 rounded-full opacity-70 -z-10 hidden md:block"></div>
    <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-red-50 rounded-full opacity-70 -z-10 hidden md:block"></div>
    
    <!-- Testimonial Header -->
    <div class="text-center max-w-4xl mx-auto mb-16 relative z-10">
        <span class="inline-block py-1 px-3 text-xs font-semibold bg-red-100 text-red-800 rounded-full tracking-wider">KHÁCH HÀNG NÓI GÌ</span>
        <h2 class="mt-4 text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">Niềm tin từ <span class="text-red-700">khách hàng</span></h2>
        <p class="mt-6 text-lg text-gray-600">Điều hành doanh nghiệp trở nên dễ dàng hơn khi có sự đồng hành từ đối tác tin cậy như Vũ Phúc Baking</p>
    </div>
    
    <!-- Testimonial Cards Slider -->
    <div class="testimonials-slider" x-data="testimonialsSlider()">
        <!-- Testimonials Wrapper -->
        <div class="overflow-hidden relative">
            <div class="flex transition-transform duration-500 ease-out" :style="{ 'transform': 'translateX(-' + (100 * activeSlide) / slidesPerView + '%)' }">
                <!-- Testimonial Card 1 -->
                <div class="testimonial-card">
                    <div class="bg-white rounded-xl shadow-lg p-8 h-full flex flex-col relative">
                        <!-- Quote Icon -->
                        <div class="absolute top-6 right-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.625 0c-1.563 0-2.978.585-4.063 1.67C1.477 2.753.891 4.168.891 5.731c0 1.094.585 2.39 1.463 3.268a4.983 4.983 0 0 0 3.56 1.475h.304c-.609 2.999-2.731 5.73-5.237 7.188a.594.594 0 0 0-.304.517.844.844 0 0 0 .152.487.635.635 0 0 0 .457.244.718.718 0 0 0 .518-.183c3.133-1.92 5.561-5.274 6.383-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274zm10.948 0c-1.58 0-2.994.585-4.079 1.67-1.068 1.084-1.671 2.5-1.671 4.06 0 1.095.586 2.392 1.463 3.27a5.032 5.032 0 0 0 3.56 1.474h.32c-.61 2.999-2.732 5.73-5.253 7.188a.61.61 0 0 0-.305.517.8.8 0 0 0 .153.487.65.65 0 0 0 .457.244.894.894 0 0 0 .533-.183c3.133-1.92 5.545-5.274 6.368-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274z" stroke="none"/>
                            </svg>
                        </div>
                        
                        <!-- Rating Stars -->
                        <div class="flex mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                        
                        <!-- Testimonial Content -->
                        <p class="text-gray-600 italic text-lg mb-6 flex-grow">"Vũ Phúc Baking không chỉ là nhà cung cấp sản phẩm mà còn là đối tác tuyệt vời trong việc hỗ trợ kỹ thuật. Đội ngũ chuyên gia của họ luôn sẵn sàng giải đáp mọi thắc mắc và đưa ra những giải pháp phù hợp."</p>
                        
                        <!-- Customer Info -->
                        <div class="flex items-center mt-auto pt-4 border-t border-gray-100">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 mr-4">
                                <img src="{{ asset('images/avatar-1.jpg') }}" alt="Nguyễn Văn A" class="w-full h-full object-cover" onerror="this.src='https://randomuser.me/api/portraits/men/32.jpg';">
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Nguyễn Văn A</h4>
                                <p class="text-sm text-gray-500">Chủ tiệm bánh Hương Vị, TP.HCM</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial Card 2 -->
                <div class="testimonial-card">
                    <div class="bg-white rounded-xl shadow-lg p-8 h-full flex flex-col relative">
                        <!-- Quote Icon -->
                        <div class="absolute top-6 right-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.625 0c-1.563 0-2.978.585-4.063 1.67C1.477 2.753.891 4.168.891 5.731c0 1.094.585 2.39 1.463 3.268a4.983 4.983 0 0 0 3.56 1.475h.304c-.609 2.999-2.731 5.73-5.237 7.188a.594.594 0 0 0-.304.517.844.844 0 0 0 .152.487.635.635 0 0 0 .457.244.718.718 0 0 0 .518-.183c3.133-1.92 5.561-5.274 6.383-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274zm10.948 0c-1.58 0-2.994.585-4.079 1.67-1.068 1.084-1.671 2.5-1.671 4.06 0 1.095.586 2.392 1.463 3.27a5.032 5.032 0 0 0 3.56 1.474h.32c-.61 2.999-2.732 5.73-5.253 7.188a.61.61 0 0 0-.305.517.8.8 0 0 0 .153.487.65.65 0 0 0 .457.244.894.894 0 0 0 .533-.183c3.133-1.92 5.545-5.274 6.368-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274z" stroke="none"/>
                            </svg>
                        </div>
                        
                        <!-- Rating Stars -->
                        <div class="flex mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                        
                        <!-- Testimonial Content -->
                        <p class="text-gray-600 italic text-lg mb-6 flex-grow">"Các khóa đào tạo của Vũ Phúc Baking đã giúp tôi nâng cao kỹ năng làm bánh một cách đáng kinh ngạc. Họ không chỉ cung cấp nguyên liệu chất lượng mà còn chia sẻ những bí quyết giúp kinh doanh thành công hơn."</p>
                        
                        <!-- Customer Info -->
                        <div class="flex items-center mt-auto pt-4 border-t border-gray-100">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 mr-4">
                                <img src="{{ asset('images/avatar-2.jpg') }}" alt="Trần Thị B" class="w-full h-full object-cover" onerror="this.src='https://randomuser.me/api/portraits/women/44.jpg';">
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Trần Thị B</h4>
                                <p class="text-sm text-gray-500">Đầu bếp bánh, Cần Thơ</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial Card 3 -->
                <div class="testimonial-card">
                    <div class="bg-white rounded-xl shadow-lg p-8 h-full flex flex-col relative">
                        <!-- Quote Icon -->
                        <div class="absolute top-6 right-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.625 0c-1.563 0-2.978.585-4.063 1.67C1.477 2.753.891 4.168.891 5.731c0 1.094.585 2.39 1.463 3.268a4.983 4.983 0 0 0 3.56 1.475h.304c-.609 2.999-2.731 5.73-5.237 7.188a.594.594 0 0 0-.304.517.844.844 0 0 0 .152.487.635.635 0 0 0 .457.244.718.718 0 0 0 .518-.183c3.133-1.92 5.561-5.274 6.383-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274zm10.948 0c-1.58 0-2.994.585-4.079 1.67-1.068 1.084-1.671 2.5-1.671 4.06 0 1.095.586 2.392 1.463 3.27a5.032 5.032 0 0 0 3.56 1.474h.32c-.61 2.999-2.732 5.73-5.253 7.188a.61.61 0 0 0-.305.517.8.8 0 0 0 .153.487.65.65 0 0 0 .457.244.894.894 0 0 0 .533-.183c3.133-1.92 5.545-5.274 6.368-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274z" stroke="none"/>
                            </svg>
                        </div>
                        
                        <!-- Rating Stars -->
                        <div class="flex mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                        
                        <!-- Testimonial Content -->
                        <p class="text-gray-600 italic text-lg mb-6 flex-grow">"Nhờ có sự tư vấn từ Vũ Phúc Baking, nhà hàng của tôi đã tiết kiệm được đáng kể chi phí và thời gian trong việc chuẩn bị các món tráng miệng. Sản phẩm của họ luôn đảm bảo chất lượng và dịch vụ giao hàng rất đáng tin cậy."</p>
                        
                        <!-- Customer Info -->
                        <div class="flex items-center mt-auto pt-4 border-t border-gray-100">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 mr-4">
                                <img src="{{ asset('images/avatar-3.jpg') }}" alt="Lê Văn C" class="w-full h-full object-cover" onerror="this.src='https://randomuser.me/api/portraits/men/67.jpg';">
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Lê Văn C</h4>
                                <p class="text-sm text-gray-500">Quản lý nhà hàng, Vĩnh Long</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial Card 4 -->
                <div class="testimonial-card">
                    <div class="bg-white rounded-xl shadow-lg p-8 h-full flex flex-col relative">
                        <!-- Quote Icon -->
                        <div class="absolute top-6 right-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.625 0c-1.563 0-2.978.585-4.063 1.67C1.477 2.753.891 4.168.891 5.731c0 1.094.585 2.39 1.463 3.268a4.983 4.983 0 0 0 3.56 1.475h.304c-.609 2.999-2.731 5.73-5.237 7.188a.594.594 0 0 0-.304.517.844.844 0 0 0 .152.487.635.635 0 0 0 .457.244.718.718 0 0 0 .518-.183c3.133-1.92 5.561-5.274 6.383-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274zm10.948 0c-1.58 0-2.994.585-4.079 1.67-1.068 1.084-1.671 2.5-1.671 4.06 0 1.095.586 2.392 1.463 3.27a5.032 5.032 0 0 0 3.56 1.474h.32c-.61 2.999-2.732 5.73-5.253 7.188a.61.61 0 0 0-.305.517.8.8 0 0 0 .153.487.65.65 0 0 0 .457.244.894.894 0 0 0 .533-.183c3.133-1.92 5.545-5.274 6.368-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274z" stroke="none"/>
                            </svg>
                        </div>
                        
                        <!-- Rating Stars -->
                        <div class="flex mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                        
                        <!-- Testimonial Content -->
                        <p class="text-gray-600 italic text-lg mb-6 flex-grow">"Là một đầu bếp bánh mới bước vào nghề, tôi rất biết ơn Vũ Phúc Baking vì đã định hướng nghề nghiệp và cung cấp những khóa đào tạo chất lượng cao. Sự hỗ trợ của họ đã giúp tôi xây dựng được thương hiệu riêng."</p>
                        
                        <!-- Customer Info -->
                        <div class="flex items-center mt-auto pt-4 border-t border-gray-100">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 mr-4">
                                <img src="{{ asset('images/avatar-4.jpg') }}" alt="Phạm Thị D" class="w-full h-full object-cover" onerror="this.src='https://randomuser.me/api/portraits/women/28.jpg';">
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Phạm Thị D</h4>
                                <p class="text-sm text-gray-500">Chủ tiệm bánh mới, Long An</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial Card 5 -->
                <div class="testimonial-card">
                    <div class="bg-white rounded-xl shadow-lg p-8 h-full flex flex-col relative">
                        <!-- Quote Icon -->
                        <div class="absolute top-6 right-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.625 0c-1.563 0-2.978.585-4.063 1.67C1.477 2.753.891 4.168.891 5.731c0 1.094.585 2.39 1.463 3.268a4.983 4.983 0 0 0 3.56 1.475h.304c-.609 2.999-2.731 5.73-5.237 7.188a.594.594 0 0 0-.304.517.844.844 0 0 0 .152.487.635.635 0 0 0 .457.244.718.718 0 0 0 .518-.183c3.133-1.92 5.561-5.274 6.383-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274zm10.948 0c-1.58 0-2.994.585-4.079 1.67-1.068 1.084-1.671 2.5-1.671 4.06 0 1.095.586 2.392 1.463 3.27a5.032 5.032 0 0 0 3.56 1.474h.32c-.61 2.999-2.732 5.73-5.253 7.188a.61.61 0 0 0-.305.517.8.8 0 0 0 .153.487.65.65 0 0 0 .457.244.894.894 0 0 0 .533-.183c3.133-1.92 5.545-5.274 6.368-9.057.213-.974.335-1.92.335-2.893 0-2.924-2.315-5.274-5.126-5.274z" stroke="none"/>
                            </svg>
                        </div>
                        
                        <!-- Rating Stars -->
                        <div class="flex mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                        
                        <!-- Testimonial Content -->
                        <p class="text-gray-600 italic text-lg mb-6 flex-grow">"Nói về dịch vụ khách hàng, Vũ Phúc Baking luôn đứng đầu. Họ không chỉ cung cấp sản phẩm chất lượng mà còn tư vấn tận tâm giúp doanh nghiệp nhỏ như chúng tôi tiết kiệm chi phí và tối ưu hóa sản xuất."</p>
                        
                        <!-- Customer Info -->
                        <div class="flex items-center mt-auto pt-4 border-t border-gray-100">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 mr-4">
                                <img src="{{ asset('images/avatar-5.jpg') }}" alt="Hoàng Văn E" class="w-full h-full object-cover" onerror="this.src='https://randomuser.me/api/portraits/men/42.jpg';">
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Hoàng Văn E</h4>
                                <p class="text-sm text-gray-500">Giám đốc chuỗi bánh, An Giang</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Testimonial Navigation -->
        <div class="flex items-center justify-center mt-10 gap-4">
            <!-- Navigation Dots -->
            <template x-for="(slide, index) in totalSlides" :key="index">
                <button @click="goToSlide(index)" class="w-3 h-3 rounded-full transition-all" 
                        :class="index === activeSlide ? 'bg-red-700 w-8' : 'bg-gray-300 hover:bg-gray-400'">
                </button>
            </template>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Testimonial Slider Styles */
    .testimonials-slider {
        margin: 0 -8px;
    }
    
    .testimonial-card {
        padding: 8px;
        width: 100%;
        flex-shrink: 0;
    }
    
    @media (min-width: 640px) {
        .testimonial-card {
            width: 50%;
        }
    }
    
    @media (min-width: 1024px) {
        .testimonial-card {
            width: 33.333%;
        }
    }
    
    @media (min-width: 1280px) {
        .testimonial-card {
            width: 25%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function testimonialsSlider() {
        return {
            activeSlide: 0,
            totalSlides: 3, // For mobile we consider 5 slides, but visually showing 1 at a time
            slidesPerView: 1,
            
            init() {
                this.updateSlidesPerView();
                window.addEventListener('resize', () => this.updateSlidesPerView());
            },
            
            updateSlidesPerView() {
                if (window.innerWidth >= 1280) {
                    this.slidesPerView = 4;
                    this.totalSlides = 2;
                } else if (window.innerWidth >= 1024) {
                    this.slidesPerView = 3;
                    this.totalSlides = 3;
                } else if (window.innerWidth >= 640) {
                    this.slidesPerView = 2;
                    this.totalSlides = 4;
                } else {
                    this.slidesPerView = 1;
                    this.totalSlides = 5;
                }
                
                // Make sure we don't go out of bounds
                if (this.activeSlide >= this.totalSlides) {
                    this.activeSlide = this.totalSlides - 1;
                }
            },
            
            next() {
                if (this.activeSlide < this.totalSlides - 1) {
                    this.activeSlide++;
                } else {
                    this.activeSlide = 0;
                }
            },
            
            prev() {
                if (this.activeSlide > 0) {
                    this.activeSlide--;
                } else {
                    this.activeSlide = this.totalSlides - 1;
                }
            },
            
            goToSlide(slideIndex) {
                this.activeSlide = slideIndex;
            }
        };
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-rotate testimonials
        const testimonialInterval = setInterval(() => {
            const slider = document.querySelector('[x-data="testimonialsSlider()"]').__x.$data;
            slider.next();
        }, 6000);
    });
</script>
@endpush