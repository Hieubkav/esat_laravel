@php
    $title = $data['title'] ?? '';
    $subtitle = $data['subtitle'] ?? '';
@endphp

<div class="container mx-auto px-4">
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl py-12 px-8 text-center text-white">
        <div class="flex justify-center mb-4">
            <svg class="w-10 h-10 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
        </div>
        @if($title)
        <h2 class="text-2xl md:text-4xl font-bold italic mb-3">{{ $title }}</h2>
        @endif
        @if($subtitle)
        <p class="text-lg md:text-xl text-white/90 tracking-wider uppercase">{{ $subtitle }}</p>
        @endif
    </div>
</div>
