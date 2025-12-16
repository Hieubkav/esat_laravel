@php
    $title = $data['title'] ?? 'Danh mục sản phẩm';
    $categories = $data['categories'] ?? [];
@endphp

<div class="container mx-auto px-4">
    <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">{{ $title }}</h2>

    @if(count($categories) > 0)
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($categories as $category)
        <a href="{{ $category['link'] ?? '#' }}" class="group block">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                @if(!empty($category['image']))
                <div class="aspect-square overflow-hidden">
                    <img src="{{ asset('storage/' . $category['image']) }}"
                         alt="{{ $category['name'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                @endif
                <div class="p-4 text-center">
                    <h3 class="font-semibold text-gray-800 group-hover:text-red-600 transition-colors">{{ $category['name'] }}</h3>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <p class="text-center text-gray-500">Chưa có danh mục sản phẩm</p>
    @endif
</div>
