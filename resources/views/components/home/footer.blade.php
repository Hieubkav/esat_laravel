@php
    // CTA
    $ctaBadge = $data['cta_badge'] ?? '';
    $ctaTitle = $data['cta_title'] ?? '';
    $ctaButtonText = $data['cta_button_text'] ?? '';
    $ctaButtonUrl = $data['cta_button_url'] ?? '';

    // Company
    $companyName = $data['company_name'] ?? 'ESAT';
    $companyDesc = $data['company_description'] ?? '';
    $phone = $data['phone'] ?? '';
    $email = $data['email'] ?? '';
    $workingHours = $data['working_hours'] ?? '';

    // Policies
    $policyTitle = $data['policy_title'] ?? 'Chính sách';
    $policies = [];
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($data["policy_{$i}_label"])) {
            $policies[] = [
                'label' => $data["policy_{$i}_label"],
                'link' => $data["policy_{$i}_link"] ?? '#',
            ];
        }
    }

    // Social
    $socialTitle = $data['social_title'] ?? 'Kết nối với chúng tôi';
    $socials = [
        'facebook' => $data['facebook_url'] ?? '',
        'zalo' => $data['zalo_url'] ?? '',
        'youtube' => $data['youtube_url'] ?? '',
        'tiktok' => $data['tiktok_url'] ?? '',
        'messenger' => $data['messenger_url'] ?? '',
    ];

    // Certifications
    $certTitle = $data['certification_title'] ?? 'Hiệp hội - Chứng nhận';
    $bctLogo = $data['bocongthuong_logo'] ?? '';
    $bctText = $data['bocongthuong_text'] ?? '';
    $bctLink = $data['bocongthuong_link'] ?? '';
    $assocTitle = $data['association_title'] ?? '';
    $associations = [];
    for ($i = 1; $i <= 4; $i++) {
        if (!empty($data["association_{$i}_logo"])) {
            $associations[] = [
                'logo' => $data["association_{$i}_logo"],
                'link' => $data["association_{$i}_link"] ?? '',
            ];
        }
    }

    $copyright = $data['copyright'] ?? '© 2025 ESAT. All Rights Reserved.';
@endphp

{{-- CTA Section --}}
@if($ctaTitle)
<section class="bg-gradient-to-r from-primary-600 to-primary-700 py-12 md:py-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left">
                @if($ctaBadge)
                <span class="inline-block px-4 py-1 bg-white/20 text-white rounded-full text-sm font-medium mb-3">{{ $ctaBadge }}</span>
                @endif
                <h2 class="text-2xl md:text-4xl font-bold text-white italic">{{ $ctaTitle }}</h2>
            </div>
            @if($ctaButtonText && $ctaButtonUrl)
            <a href="{{ $ctaButtonUrl }}" class="px-8 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                {{ $ctaButtonText }}
            </a>
            @endif
        </div>
    </div>
</section>
@endif

<footer class="bg-gray-50 border-t border-gray-100">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
            {{-- Column 1 - Company Info --}}
            <div class="flex flex-col justify-start">
                <h3 class="text-lg font-semibold text-primary-700 mb-4">{{ $companyName }}</h3>
                <div class="space-y-2 text-gray-600 text-sm">
                    @if($companyDesc)
                    <p>{{ $companyDesc }}</p>
                    @endif
                    @if($phone)
                    <p class="flex items-center">
                        <span class="mr-2">☎</span>
                        <span>{{ $phone }}</span>
                    </p>
                    @endif
                    @if($email)
                    <p class="flex items-center">
                        <span class="mr-2">📧</span>
                        <span>{{ $email }}</span>
                    </p>
                    @endif
                    @if($workingHours)
                    <p class="flex items-center">
                        <span class="mr-2">🕒</span>
                        <span>{{ $workingHours }}</span>
                    </p>
                    @endif
                </div>
            </div>

            {{-- Column 2 - Policies & Social --}}
            <div class="flex flex-col justify-start">
                <h3 class="text-lg font-semibold text-primary-700 mb-4">{{ $policyTitle }}</h3>
                <ul class="space-y-2 text-gray-600 text-sm">
                    @foreach($policies as $policy)
                    <li>
                        <a href="{{ $policy['link'] }}" class="hover:text-primary-700 transition-colors">{{ $policy['label'] }}</a>
                    </li>
                    @endforeach
                </ul>

                {{-- Social Links --}}
                @php
                    $hasSocials = !empty($socials['facebook']) || !empty($socials['zalo']) || !empty($socials['youtube']) || !empty($socials['tiktok']) || !empty($socials['messenger']);
                @endphp
                @if($hasSocials)
                <div class="mt-6">
                    <h4 class="text-md font-medium text-primary-700 mb-3">{{ $socialTitle }}</h4>
                    <div class="flex items-center gap-3">
                        @if($socials['facebook'])
                        <a href="{{ $socials['facebook'] }}" target="_blank" rel="noopener noreferrer"
                           class="hover:opacity-80 transition-all duration-300 transform hover:scale-110"
                           aria-label="Facebook">
                            <img src="{{ asset('images/icon_facebook.webp') }}" alt="Facebook" class="h-6 w-6 icon-primary-filter">
                        </a>
                        @endif
                        @if($socials['zalo'])
                        <a href="{{ $socials['zalo'] }}" target="_blank" rel="noopener noreferrer"
                           class="hover:opacity-80 transition-all duration-300 transform hover:scale-110"
                           aria-label="Zalo">
                            <img src="{{ asset('images/icon_zalo.webp') }}" alt="Zalo" class="h-6 w-6 icon-primary-filter">
                        </a>
                        @endif
                        @if($socials['youtube'])
                        <a href="{{ $socials['youtube'] }}" target="_blank" rel="noopener noreferrer"
                           class="hover:opacity-80 transition-all duration-300 transform hover:scale-110"
                           aria-label="Youtube">
                            <img src="{{ asset('images/youtube_icon.webp') }}" alt="Youtube" class="h-6 w-6 icon-primary-filter">
                        </a>
                        @endif
                        @if($socials['tiktok'])
                        <a href="{{ $socials['tiktok'] }}" target="_blank" rel="noopener noreferrer"
                           class="hover:opacity-80 transition-all duration-300 transform hover:scale-110"
                           aria-label="Tiktok">
                            <img src="{{ asset('images/tiktok_icon.webp') }}" alt="Tiktok" class="h-6 w-6 icon-primary-filter">
                        </a>
                        @endif
                        @if($socials['messenger'])
                        <a href="{{ $socials['messenger'] }}" target="_blank" rel="noopener noreferrer"
                           class="hover:opacity-80 transition-all duration-300 transform hover:scale-110"
                           aria-label="Messenger">
                            <img src="{{ asset('images/icon_messenger.webp') }}" alt="Messenger" class="h-6 w-6 icon-primary-filter">
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Column 3 - Certifications --}}
            <div class="flex flex-col justify-start">
                <h3 class="text-lg font-semibold text-primary-700 mb-4">{{ $certTitle }}</h3>

                {{-- Logo Bộ Công Thương --}}
                @if($bctLogo || $bctText)
                <div class="mb-6">
                    <div class="flex justify-start mb-2">
                        @if($bctLink)
                        <a href="{{ $bctLink }}" target="_blank" rel="noopener noreferrer">
                        @endif
                            @if($bctLogo)
                            <img src="{{ asset('storage/' . $bctLogo) }}"
                                 alt="Đã đăng ký với Bộ Công Thương"
                                 class="h-20 hover:opacity-80 transition-opacity">
                            @endif
                        @if($bctLink)
                        </a>
                        @endif
                    </div>
                    @if($bctText)
                    <p class="text-xs text-gray-500 text-left">{{ $bctText }}</p>
                    @endif
                </div>
                @endif

                {{-- Logo các hiệp hội --}}
                @if($assocTitle && count($associations) > 0)
                <div class="w-full">
                    <p class="text-xs text-gray-500 mb-3 text-left">{{ $assocTitle }}</p>
                    <div class="flex flex-wrap justify-start items-center gap-4">
                        @foreach($associations as $assoc)
                            @if(!empty($assoc['link']))
                            <a href="{{ $assoc['link'] }}" target="_blank" class="hover:opacity-80 transition-opacity">
                            @endif
                                <img src="{{ asset('storage/' . $assoc['logo']) }}"
                                     alt="Hiệp hội"
                                     class="h-10">
                            @if(!empty($assoc['link']))
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="bg-primary-700 py-4 text-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm">{{ $copyright }}</p>
            </div>
        </div>
    </div>
</footer>
