@php
    $statsData = webDesignData('stats-counter');
    $stats = webDesignContent('stats-counter', 'stats', [
        ['number' => '8500', 'label' => 'Khách hàng'],
        ['number' => '150', 'label' => 'Đối tác'],
        ['number' => '1200', 'label' => 'Sản phẩm'],
        ['number' => '63', 'label' => 'Khu vực phân phối'],
    ]);
@endphp

<div class="container mx-auto px-4">
    <!-- Animated Stats Counters -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        @foreach($stats as $index => $stat)
            @if($index < 4)
                <div class="text-center p-6 rounded-lg bg-white shadow-lg hover:shadow-xl transition-shadow border-t-4 border-red-600">
                    <div class="text-4xl font-bold text-red-700 counter" data-target="{{ $stat['number'] ?? '0' }}">{{ $stat['number'] ?? '0' }}</div>
                    <p class="text-gray-600 mt-2">{{ $stat['label'] ?? 'Thống kê' }}</p>
                </div>
            @endif
        @endforeach
    </div>
</div>


