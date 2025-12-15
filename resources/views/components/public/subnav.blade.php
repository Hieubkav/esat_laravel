@php
    // Sử dụng dữ liệu từ ViewServiceProvider với fallback
    $settingsData = $globalSettings ?? $settings ?? null;

    // Fallback: nếu không có dữ liệu từ ViewServiceProvider, lấy trực tiếp từ model
    if (!$settingsData) {
        try {
            $settingsData = \App\Models\Setting::where('status', 'active')->first();
        } catch (\Exception $e) {
            $settingsData = null;
        }
    }

    // Lấy các trường cần thiết với kiểm tra an toàn
    $email = $settingsData ? ($settingsData->email ?? null) : null;
    $hotline = $settingsData ? ($settingsData->hotline ?? null) : null;
    $facebook_link = $settingsData ? ($settingsData->facebook_link ?? null) : null;
    $zalo_link = $settingsData ? ($settingsData->zalo_link ?? null) : null;
    $youtube_link = $settingsData ? ($settingsData->youtube_link ?? null) : null;
    $tiktok_link = $settingsData ? ($settingsData->tiktok_link ?? null) : null;
    $messenger_link = $settingsData ? ($settingsData->messenger_link ?? null) : null;

    // Tạo mảng contact info với kiểm tra dữ liệu
    $contactInfo = [];
    if (!empty($email)) {
        $contactInfo[] = [
            'type' => 'email',
            'value' => $email,
            'href' => 'mailto:' . $email,
            'icon' => 'email',
            'label' => $email
        ];
    }
    if (!empty($hotline)) {
        $contactInfo[] = [
            'type' => 'phone',
            'value' => $hotline,
            'href' => 'tel:' . $hotline,
            'icon' => 'phone',
            'label' => $hotline
        ];
    }

    // Tạo mảng social links với kiểm tra dữ liệu
    $socialLinks = [];
    if (!empty($facebook_link)) {
        $socialLinks[] = [
            'type' => 'facebook',
            'href' => $facebook_link,
            'icon' => 'facebook',
            'label' => 'Facebook'
        ];
    }
    if (!empty($zalo_link)) {
        $socialLinks[] = [
            'type' => 'zalo',
            'href' => $zalo_link,
            'icon' => 'zalo',
            'label' => 'Zalo'
        ];
    }
    if (!empty($youtube_link)) {
        $socialLinks[] = [
            'type' => 'youtube',
            'href' => $youtube_link,
            'icon' => 'youtube',
            'label' => 'YouTube'
        ];
    }
    if (!empty($tiktok_link)) {
        $socialLinks[] = [
            'type' => 'tiktok',
            'href' => $tiktok_link,
            'icon' => 'tiktok',
            'label' => 'TikTok'
        ];
    }
    if (!empty($messenger_link)) {
        $socialLinks[] = [
            'type' => 'messenger',
            'href' => $messenger_link,
            'icon' => 'messenger',
            'label' => 'Messenger'
        ];
    }

    // Kiểm tra có dữ liệu để hiển thị không
    $hasContactInfo = !empty($contactInfo);
    $hasSocialLinks = !empty($socialLinks);
    $hasAnyData = $hasContactInfo || $hasSocialLinks;
@endphp

{{-- Chỉ hiển thị subnav nếu có dữ liệu --}}
@if($hasAnyData)
<div class="bg-red-700 text-white py-2 md:py-2.5 shadow-sm">
    <div class="container mx-auto px-4">

        {{-- MOBILE VERSION --}}
        <div class="md:hidden">
            @if($hasContactInfo)
                <div class="flex justify-center items-center">
                    {{-- Chỉ hiển thị Contact Info trên mobile, ẩn Social Links --}}
                    <div class="flex items-center gap-3">
                        @foreach($contactInfo as $contact)
                            <a href="{{ $contact['href'] }}"
                               class="flex items-center gap-1.5 text-xs hover:text-red-200 transition-colors duration-300"
                               aria-label="{{ $contact['label'] }}">
                                @if($contact['icon'] === 'email')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                @elseif($contact['icon'] === 'phone')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                @endif
                                <span class="truncate">{{ $contact['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- DESKTOP VERSION --}}
        <div class="hidden md:block">
            @if($hasContactInfo || $hasSocialLinks)
                <div class="flex justify-between items-center text-sm">
                    {{-- Contact Info trên desktop --}}
                    @if($hasContactInfo)
                        <div class="flex items-center gap-6">
                            @foreach($contactInfo as $contact)
                                <a href="{{ $contact['href'] }}"
                                   class="flex items-center gap-2 hover:text-red-200 transition-colors duration-300 group"
                                   aria-label="{{ $contact['label'] }}">
                                    @if($contact['icon'] === 'email')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0 group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    @elseif($contact['icon'] === 'phone')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0 group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    @endif
                                    <span>{{ $contact['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div></div> {{-- Spacer khi không có contact info --}}
                    @endif

                    {{-- Social Links trên desktop --}}
                    @if($hasSocialLinks)
                        <div class="flex items-center gap-4">
                            @foreach($socialLinks as $social)
                                <a href="{{ $social['href'] }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="hover:text-red-200 transition-all duration-300 transform hover:scale-110 group"
                                   aria-label="{{ $social['label'] }}">
                                    @if($social['icon'] === 'facebook')
                                        <div class="bg-white rounded-full p-1.5 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <img src="{{ asset('images/icon_facebook.webp') }}" alt="Facebook" class="h-4 w-4">
                                        </div>
                                    @elseif($social['icon'] === 'zalo')
                                        <div class="bg-white rounded-full p-1.5 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <img src="{{ asset('images/icon_zalo.webp') }}" alt="Zalo" class="h-4 w-4">
                                        </div>
                                    @elseif($social['icon'] === 'youtube')
                                        <div class="bg-white rounded-full p-1.5 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <img src="{{ asset('images/youtube_icon.webp') }}" alt="YouTube" class="h-4 w-4">
                                        </div>
                                    @elseif($social['icon'] === 'tiktok')
                                        <div class="bg-white rounded-full p-1.5 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <img src="{{ asset('images/tiktok_icon.webp') }}" alt="TikTok" class="h-4 w-4">
                                        </div>
                                    @elseif($social['icon'] === 'messenger')
                                        <div class="bg-white rounded-full p-1.5 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <img src="{{ asset('images/icon_messenger.webp') }}" alt="Messenger" class="h-4 w-4">
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endif