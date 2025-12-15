{{--
    Component mẫu minh họa nguyên tắc "responsive với dữ liệu trống"
    Áp dụng cho tất cả component trong tương lai
--}}

@php
    // Ví dụ dữ liệu từ ViewServiceProvider hoặc props
    $items = $items ?? [];
    $title = $title ?? '';
    $description = $description ?? '';
    $showButton = $showButton ?? false;
    $buttonText = $buttonText ?? 'Xem thêm';
    $buttonLink = $buttonLink ?? '#';
    
    // Logic kiểm tra dữ liệu thông minh
    $hasItems = !empty($items) && is_countable($items) && count($items) > 0;
    $hasTitle = !empty($title) && trim($title) !== '';
    $hasDescription = !empty($description) && trim($description) !== '';
    $hasButton = $showButton && !empty($buttonText) && !empty($buttonLink);
    
    // Kiểm tra có dữ liệu để hiển thị không
    $hasAnyContent = $hasItems || $hasTitle || $hasDescription;
    
    // Tính toán layout dựa trên số lượng items
    $itemCount = $hasItems ? count($items) : 0;
    $gridCols = match(true) {
        $itemCount >= 4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        $itemCount === 3 => 'grid-cols-1 md:grid-cols-3',
        $itemCount === 2 => 'grid-cols-1 md:grid-cols-2',
        $itemCount === 1 => 'grid-cols-1 max-w-md mx-auto',
        default => 'grid-cols-1'
    };
@endphp

{{-- Chỉ hiển thị component nếu có dữ liệu --}}
@if($hasAnyContent)
<section class="py-8 md:py-12">
    <div class="container mx-auto px-4">
        
        {{-- Header Section - Chỉ hiển thị nếu có title hoặc description --}}
        @if($hasTitle || $hasDescription)
            <div class="text-center mb-8 md:mb-12">
                {{-- Title - Chỉ hiển thị nếu có dữ liệu --}}
                @if($hasTitle)
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        {{ $title }}
                    </h2>
                @endif
                
                {{-- Description - Chỉ hiển thị nếu có dữ liệu --}}
                @if($hasDescription)
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                        {{ $description }}
                    </p>
                @endif
            </div>
        @endif
        
        {{-- Items Grid - Chỉ hiển thị nếu có items --}}
        @if($hasItems)
            <div class="grid {{ $gridCols }} gap-6">
                @foreach($items as $index => $item)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        {{-- Item Image - Chỉ hiển thị nếu có image --}}
                        @if(!empty($item['image']))
                            <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                                <img src="{{ $item['image'] }}" 
                                     alt="{{ $item['title'] ?? 'Image' }}" 
                                     class="w-full h-48 object-cover">
                            </div>
                        @endif
                        
                        <div class="p-6">
                            {{-- Item Title - Chỉ hiển thị nếu có title --}}
                            @if(!empty($item['title']))
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                    {{ $item['title'] }}
                                </h3>
                            @endif
                            
                            {{-- Item Description - Chỉ hiển thị nếu có description --}}
                            @if(!empty($item['description']))
                                <p class="text-gray-600 mb-4">
                                    {{ $item['description'] }}
                                </p>
                            @endif
                            
                            {{-- Item Price - Chỉ hiển thị nếu có price --}}
                            @if(!empty($item['price']))
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-red-600">
                                        {{ number_format($item['price']) }}đ
                                    </span>
                                    
                                    {{-- Compare Price - Chỉ hiển thị nếu có compare_price --}}
                                    @if(!empty($item['compare_price']) && $item['compare_price'] < $item['price'])
                                        <span class="text-gray-400 line-through">
                                            {{ number_format($item['compare_price']) }}đ
                                        </span>
                                    @endif
                                </div>
                            @endif
                            
                            {{-- Item Link - Chỉ hiển thị nếu có link --}}
                            @if(!empty($item['link']))
                                <a href="{{ $item['link'] }}" 
                                   class="inline-block mt-4 text-red-600 hover:text-red-700 font-medium">
                                    Xem chi tiết →
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        {{-- Button Section - Chỉ hiển thị nếu có button --}}
        @if($hasButton)
            <div class="text-center mt-8 md:mt-12">
                <a href="{{ $buttonLink }}" 
                   class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-300">
                    {{ $buttonText }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        @endif
    </div>
</section>

{{-- Fallback message cho development --}}
@elseif(config('app.debug'))
    <div class="py-8">
        <div class="container mx-auto px-4">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                <p class="text-yellow-800">
                    <strong>Component ẩn:</strong> Không có dữ liệu để hiển thị.
                    <br><small>Thông báo này chỉ hiển thị trong môi trường development.</small>
                </p>
            </div>
        </div>
    </div>
@endif

{{--
    NGUYÊN TẮC QUAN TRỌNG CHO TẤT CẢ COMPONENT:
    
    1. LUÔN KIỂM TRA DỮ LIỆU:
       - Sử dụng isset() và !empty() trước khi hiển thị
       - Kiểm tra cả null và chuỗi rỗng
       - Xử lý mảng rỗng và object rỗng
    
    2. LAYOUT LINH HOẠT:
       - Grid tự động điều chỉnh theo số lượng items
       - Flexbox cho spacing tự động
       - Responsive breakpoints phù hợp
    
    3. HIỂN THỊ THÔNG MINH:
       - Ẩn hoàn toàn element nếu không có dữ liệu
       - Không để lại khoảng trống không cần thiết
       - Fallback graceful cho missing data
    
    4. PERFORMANCE:
       - Tính toán logic ở đầu component
       - Tránh logic phức tạp trong template
       - Cache kết quả kiểm tra dữ liệu
    
    5. ACCESSIBILITY:
       - Alt text cho images
       - Aria labels cho interactive elements
       - Semantic HTML structure
    
    6. UX/UI:
       - Hover effects mượt mà
       - Transition animations
       - Consistent spacing và typography
--}}
