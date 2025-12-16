@php
    $items = $data['items'] ?? [];
@endphp

<div class="container mx-auto px-4">
    <!-- Animated Stats Counters -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        @foreach($items as $index => $item)
            @if($index < 4)
                <div class="text-center p-6 rounded-lg bg-white shadow-lg hover:shadow-xl transition-shadow border-t-4 border-primary-600">
                    <div class="text-4xl font-bold text-primary-700 counter" data-target="{{ $item['value'] ?? '0' }}">{{ $item['value'] ?? '0' }}</div>
                    <p class="text-gray-600 mt-2">{{ $item['label'] ?? 'Thống kê' }}</p>
                </div>
            @endif
        @endforeach
    </div>
</div>
