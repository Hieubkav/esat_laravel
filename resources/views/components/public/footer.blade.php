<footer class="bg-gray-50 border-t border-gray-100">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
            <!-- Cột 1: Giới thiệu & Liên hệ -->
            <div class="flex flex-col justify-start">
                <h3 class="text-lg font-semibold text-red-700 mb-4">CÔNG TY TNHH SX TM DV VŨ PHÚC</h3>
                <div class="space-y-2 text-gray-600 text-sm">
                    <p>Giấy phép kinh doanh số 1800935879, cấp ngày 29/4/2009</p>
                    <p>Chịu trách nhiệm: Trần Uy Vũ – Tổng Giám đốc</p>
                    <p class="flex items-center">
                        <span class="mr-2">☎</span>
                        <span>1900 6363 40</span>
                    </p>
                    <p class="flex items-center">
                        <span class="mr-2">📧</span>
                        <span>contact@vuphucbaking.com</span>
                    </p>
                    <p class="flex items-center">
                        <span class="mr-2">🕒</span>
                        <span>7:30 - 17:00 (Thứ 2 - Thứ 6) & 7:30 - 12:00 (Thứ 7)</span>
                    </p>
                </div>
            </div>

            <!-- Cột 2: Chính sách & Liên kết -->
            <div class="flex flex-col justify-start">
                <h3 class="text-lg font-semibold text-red-700 mb-4">Chính sách</h3>
                <ul class="space-y-2 text-gray-600 text-sm">
                    @php
                        $policies = webDesignContent('footer', 'policies', [
                            ['title' => 'Chính sách & Điều khoản mua bán hàng hóa', 'url' => '#'],
                            ['title' => 'Hệ thống đại lý & điểm bán hàng', 'url' => '#'],
                            ['title' => 'Bảo mật & Quyền riêng tư', 'url' => '#'],
                        ]);
                    @endphp
                    @foreach($policies as $policy)
                        <li><a href="{{ $policy['url'] ?? '#' }}" class="hover:text-red-700 transition-colors">{{ $policy['title'] ?? '' }}</a></li>
                    @endforeach
                </ul>

                <!-- Kết nối với chúng tôi -->
                @php
                    $settingsData = $globalSettings ?? $settings ?? null;
                    if (!$settingsData) {
                        try {
                            $settingsData = \App\Models\Setting::where('status', 'active')->first();
                        } catch (\Exception $e) {
                            $settingsData = null;
                        }
                    }
                    $socialLinks = [];
                    $socialTypes = ['facebook', 'zalo', 'youtube', 'tiktok', 'messenger'];
                    foreach($socialTypes as $type) {
                        $link = $settingsData ? ($settingsData->{$type . '_link'} ?? null) : null;
                        if (!empty($link)) {
                            $socialLinks[] = ['type' => $type, 'href' => $link, 'label' => ucfirst($type)];
                        }
                    }
                @endphp

                @if(!empty($socialLinks))
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-red-700 mb-3">Kết nối với chúng tôi</h4>
                        <div class="flex items-center gap-3">
                            @foreach($socialLinks as $social)
                                <a href="{{ $social['href'] }}" target="_blank" rel="noopener noreferrer"
                                   class="hover:opacity-80 transition-all duration-300 transform hover:scale-110"
                                   aria-label="{{ $social['label'] }}">
                                    @if($social['type'] === 'youtube')
                                        <img src="{{ asset('images/youtube_icon.webp') }}" alt="{{ $social['label'] }}" class="h-6 w-6">
                                    @elseif($social['type'] === 'tiktok')
                                        <img src="{{ asset('images/tiktok_icon.webp') }}" alt="{{ $social['label'] }}" class="h-6 w-6">
                                    @else
                                        <img src="{{ asset('images/icon_' . $social['type'] . '.webp') }}" alt="{{ $social['label'] }}" class="h-6 w-6">
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Cột 3: Hiệp hội – Chứng nhận -->
            <div class="flex flex-col justify-start">
                <h3 class="text-lg font-semibold text-red-700 mb-4">Hiệp hội – Chứng nhận</h3>

                <!-- Logo Bộ Công Thương -->
                <div class="mb-6">
                    <div class="flex justify-start mb-2">
                        <a href="http://online.gov.vn/Home/WebDetails/29114?AspxAutoDetectCookieSupport=1" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('images/bo_cong_thuong.webp') }}"
                                 alt="Đã đăng ký với Bộ Công Thương"
                                 class="h-20 hover:opacity-80 transition-opacity"
                                 onerror="this.style.display='none'">
                        </a>
                    </div>
                    <p class="text-xs text-gray-500 text-left">Đã đăng ký với Bộ Công Thương</p>
                </div>

                <!-- Logo các hiệp hội -->
                @if(isset($associations) && !empty($associations) && $associations->count() > 0)
                    <div class="w-full">
                        <p class="text-xs text-gray-500 mb-3 text-left">Thành viên các hiệp hội</p>
                        <div class="flex flex-wrap justify-start items-center gap-4">
                            @foreach($associations as $association)
                                @if(!empty($association->image_link))
                                    @if(!empty($association->website_link))
                                        <a href="{{ $association->website_link }}" target="_blank" title="{{ $association->name }}">
                                            <img src="{{ asset('storage/' . $association->image_link) }}"
                                                 alt="{{ $association->name }}"
                                                 class="h-10 hover:opacity-80 transition-opacity"
                                                 onerror="this.style.display='none'">
                                        </a>
                                    @else
                                        <img src="{{ asset('storage/' . $association->image_link) }}"
                                             alt="{{ $association->name }}"
                                             class="h-10"
                                             onerror="this.style.display='none'">
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="bg-red-700 py-4 text-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm">
                    @php
                        $copyright = webDesignContent('footer', 'copyright', '© ' . date('Y') . ' Copyright by VUPHUC BAKING - All Rights Reserved');
                    @endphp
                    {{ $copyright }}
                </p>
                {{-- <p class="text-sm mt-2 md:mt-0">Thiết kế bởi <a href="#" class="text-white hover:text-red-200">Phương Việt</a></p> --}}
            </div>
        </div>
    </div>
</footer>