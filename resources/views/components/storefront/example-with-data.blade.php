{{-- 
    Component mẫu sử dụng dữ liệu từ ViewServiceProvider
    Dữ liệu được tự động inject vào view thông qua ViewServiceProvider
--}}

<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">Sử dụng dữ liệu từ ViewServiceProvider</h2>
    
    {{-- Sử dụng dữ liệu sliders --}}
    @if(isset($sliders) && $sliders->count() > 0)
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Sliders từ Database</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($sliders as $slider)
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <h4 class="font-medium">{{ $slider->title }}</h4>
                        <p class="text-gray-600 text-sm">{{ $slider->description }}</p>
                        <span class="text-xs text-gray-400">Order: {{ $slider->order }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Sử dụng dữ liệu categories --}}
    @if(isset($categories) && $categories->count() > 0)
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Categories từ Database</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($categories as $category)
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <h4 class="font-medium text-sm">{{ $category->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $category->slug }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Sử dụng dữ liệu featuredProducts --}}
    @if(isset($featuredProducts) && $featuredProducts->count() > 0)
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Sản phẩm nổi bật từ Database</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($featuredProducts as $product)
                    <div class="bg-white border rounded-lg p-4">
                        <h4 class="font-medium text-sm mb-2">{{ $product->name }}</h4>
                        <p class="text-red-600 font-bold">{{ formatPrice($product->price) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Sử dụng dữ liệu partners --}}
    @if(isset($partners) && $partners->count() > 0)
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Đối tác từ Database</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($partners as $partner)
                    <div class="bg-white border rounded-lg p-3 text-center">
                        <h4 class="font-medium text-xs">{{ $partner->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $partner->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Sử dụng dữ liệu settings --}}
    @if(isset($globalSettings))
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Settings từ Database</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <p><strong>Site Name:</strong> {{ $globalSettings->site_name ?? 'N/A' }}</p>
                <p><strong>Hotline:</strong> {{ $globalSettings->hotline ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $globalSettings->email ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $globalSettings->address ?? 'N/A' }}</p>
            </div>
        </div>
    @endif

    {{-- Fallback message --}}
    @if(!isset($sliders) && !isset($categories) && !isset($featuredProducts))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <strong>Lưu ý:</strong> Dữ liệu chưa được load từ ViewServiceProvider. 
                Hãy đảm bảo component này được gọi từ view có sử dụng ViewServiceProvider.
            </p>
        </div>
    @endif
</div>
