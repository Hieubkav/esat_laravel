@php
    $ctaData = webDesignData('homepage-cta');
    $isVisible = webDesignVisible('homepage-cta');
@endphp

@if($isVisible)
<div class="absolute inset-0 opacity-10">
    <div class="absolute inset-0 bg-pattern"></div>
</div>
<div class="container mx-auto px-4 relative z-10">
    <div class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-16">
        <div class="text-center md:text-left max-w-2xl">
            <span class="text-xs uppercase tracking-widest font-semibold bg-white bg-opacity-20 px-3 py-1 rounded-full mb-4 inline-block">
                {{ $ctaData?->subtitle ?? 'Trải nghiệm đẳng cấp' }}
            </span>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 leading-tight">
                {!! $ctaData?->title ?? 'Bắt đầu hành trình<br>với <span class="italic">Vũ Phúc Baking</span>' !!}
            </h2>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ $ctaData?->button_url ?? route('ecomerce.index') }}"
               class="px-8 py-4 bg-white text-red-700 font-semibold rounded-lg hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-center min-w-[180px]">
                {{ $ctaData?->button_text ?? 'Mua sắm ngay' }}
            </a>
        </div>
    </div>
</div>
@endif