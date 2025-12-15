@php
    // Simple, shared renderer for search result thumbnail
    // Props: $result (array), $size (Tailwind size classes)
    $size = $size ?? 'w-12 h-12';
    $rounded = $rounded ?? 'rounded-lg';
    $classes = trim(($class ?? '') . ' ' . $size . ' object-cover ' . $rounded . ' mr-3 flex-shrink-0 shadow-sm');
    $title = isset($result['title']) ? $result['title'] : 'Hình ảnh';
    $type = $result['type'] ?? null;
    $hasImage = isset($result['image']) && $result['image'];
@endphp

@if($hasImage)
    @php
        $src = $type === 'product'
            ? $result['image']
            : asset('storage/' . $result['image']);
    @endphp
    <img
        src="{{ $src }}"
        alt="{{ $title }}"
        class="{{ $classes }}"
        loading="lazy"
        decoding="async"
        onerror="this.src='{{ asset('images/no-image.svg') }}'; this.onerror=null;"
    >
@else
    <div class="{{ $size }} bg-gradient-to-br from-red-100 to-red-200 dark:from-gray-600 dark:to-gray-700 {{ $rounded }} mr-3 flex-shrink-0 flex items-center justify-center">
        @if($type === 'product')
            <i class="fas fa-birthday-cake text-red-500 dark:text-red-400 text-lg"></i>
        @else
            <i class="fas fa-newspaper text-red-500 dark:text-red-400 text-lg"></i>
        @endif
    </div>
@endif

