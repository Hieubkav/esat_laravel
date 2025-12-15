<div class="container mx-auto px-4 relative z-10">
    <!-- Pattern background -->
    <div class="absolute inset-0 bg-pattern opacity-20"></div>
    
    <div class="relative z-20 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="md:w-1/2 text-center md:text-left">
            <span class="inline-block px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-semibold mb-4">KHUYẾN MÃI ĐẶC BIỆT</span>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 leading-tight">Đăng ký ngay hôm nay<br>và nhận <span class="italic underline decoration-wavy decoration-white decoration-2 underline-offset-4">ưu đãi 20%</span></h2>
            <p class="text-lg mb-8 text-white text-opacity-90">Còn chần chờ gì nữa? Hãy đăng ký ngay hôm nay để bắt đầu hành trình trở thành chuyên gia làm bánh cùng Vũ Phúc Baking!</p>
            
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-center md:justify-start">
                <a href="#course-registration" class="px-8 py-4 bg-white text-red-700 font-bold rounded-lg hover:bg-gray-100 transition-all shadow-lg hover:shadow-2xl transform hover:-translate-y-1 w-full sm:w-auto text-center">
                    Đăng ký ngay
                </a>
                <a href="#course-content" class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-lg hover:bg-white hover:text-red-700 transition-all hover:shadow-lg w-full sm:w-auto text-center">
                    Tìm hiểu thêm
                </a>
            </div>
            
            <!-- countdown timer -->
            <div class="mt-10 bg-white bg-opacity-20 rounded-lg p-5 inline-block">
                <p class="text-white mb-3 font-medium">Ưu đãi kết thúc sau:</p>
                <div class="flex justify-center md:justify-start gap-4">
                    <div class="text-center">
                        <div class="bg-white text-red-700 w-14 h-14 rounded-lg flex items-center justify-center text-2xl font-bold">03</div>
                        <p class="text-white text-sm mt-1">Ngày</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-white text-red-700 w-14 h-14 rounded-lg flex items-center justify-center text-2xl font-bold">21</div>
                        <p class="text-white text-sm mt-1">Giờ</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-white text-red-700 w-14 h-14 rounded-lg flex items-center justify-center text-2xl font-bold">45</div>
                        <p class="text-white text-sm mt-1">Phút</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-white text-red-700 w-14 h-14 rounded-lg flex items-center justify-center text-2xl font-bold">19</div>
                        <p class="text-white text-sm mt-1">Giây</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="md:w-2/5">
            <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 transform rotate-1 hover:rotate-0 transition-all duration-300">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Đăng ký tư vấn miễn phí</h3>
                <form class="space-y-4">
                    <div>
                        <label for="name" class="block text-gray-700 font-medium mb-1">Họ và tên</label>
                        <input type="text" id="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" placeholder="Nhập họ và tên của bạn">
                    </div>
                    <div>
                        <label for="phone" class="block text-gray-700 font-medium mb-1">Số điện thoại</label>
                        <input type="tel" id="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" placeholder="Nhập số điện thoại của bạn">
                    </div>
                    <div>
                        <label for="course" class="block text-gray-700 font-medium mb-1">Khóa học quan tâm</label>
                        <select id="course" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent bg-white">
                            <option value="" selected disabled>Chọn khóa học</option>
                            <option value="basic">Khóa học làm bánh cơ bản</option>
                            <option value="intermediate">Khóa học làm bánh trung cấp</option>
                            <option value="advanced">Khóa học làm bánh nâng cao</option>
                            <option value="professional">Khóa học chuyên nghiệp</option>
                        </select>
                    </div>
                    <div>
                        <label for="message" class="block text-gray-700 font-medium mb-1">Lời nhắn</label>
                        <textarea id="message" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" placeholder="Bạn có yêu cầu đặc biệt nào không?"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-lg hover:shadow-xl">
                        Đăng ký ngay
                    </button>
                    <p class="text-gray-500 text-sm text-center">Chúng tôi sẽ liên hệ với bạn trong vòng 24 giờ</p>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Trust badges -->
    <div class="mt-16 flex flex-wrap justify-center gap-6 md:gap-10 relative z-20">
        <div class="bg-white bg-opacity-20 backdrop-blur-sm p-3 md:p-4 rounded-lg flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span class="ml-2 text-white font-medium">Bảo đảm chất lượng</span>
        </div>
        <div class="bg-white bg-opacity-20 backdrop-blur-sm p-3 md:p-4 rounded-lg flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="ml-2 text-white font-medium">Hoàn tiền trong 7 ngày</span>
        </div>
        <div class="bg-white bg-opacity-20 backdrop-blur-sm p-3 md:p-4 rounded-lg flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="ml-2 text-white font-medium">Đào tạo tại cơ sở hiện đại</span>
        </div>
        <div class="bg-white bg-opacity-20 backdrop-blur-sm p-3 md:p-4 rounded-lg flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="ml-2 text-white font-medium">Lớp học quy mô nhỏ</span>
        </div>
    </div>
</div>

<script>
    // Simple countdown timer
    document.addEventListener('DOMContentLoaded', function() {
        // Set the countdown date (3 days from now)
        const countDownDate = new Date();
        countDownDate.setDate(countDownDate.getDate() + 3);
        
        // Update the countdown every 1 second
        const x = setInterval(function() {
            // Get today's date and time
            const now = new Date().getTime();
            
            // Find the distance between now and the countdown date
            const distance = countDownDate - now;
            
            // Time calculations for days, hours, minutes and seconds
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Display the result
            document.querySelectorAll('.bg-white.text-red-700')[0].textContent = String(days).padStart(2, '0');
            document.querySelectorAll('.bg-white.text-red-700')[1].textContent = String(hours).padStart(2, '0');
            document.querySelectorAll('.bg-white.text-red-700')[2].textContent = String(minutes).padStart(2, '0');
            document.querySelectorAll('.bg-white.text-red-700')[3].textContent = String(seconds).padStart(2, '0');
            
            // If the countdown is finished
            if (distance < 0) {
                clearInterval(x);
                document.querySelectorAll('.bg-white.text-red-700').forEach(el => {
                    el.textContent = "00";
                });
            }
        }, 1000);
    });
</script>