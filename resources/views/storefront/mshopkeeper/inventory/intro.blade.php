@extends('layouts.shop')

@section('title', 'Kho Hàng - Sản Phẩm Từ MShopKeeper')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Kho Hàng Sản Phẩm</h1>
                <p class="text-xl mb-8 opacity-90">Khám phá các sản phẩm từ hệ thống MShopKeeper</p>
                
                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto">
                    <form action="{{ route('mshopkeeper.inventory.index') }}" method="GET" class="flex">
                        <input type="text"
                               name="search"
                               placeholder="Tìm kiếm sản phẩm, mã hàng..."
                               class="flex-1 px-6 py-4 text-gray-900 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-300"
                               value="{{ request('search') }}">
                        <button type="submit"
                                class="bg-yellow-500 hover:bg-yellow-600 px-8 py-4 rounded-r-lg font-semibold transition-colors">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Categories -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Danh Mục Sản Phẩm</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Hàng Hoá -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold">Hàng Hoá</h3>
                                <p class="opacity-90">Sản phẩm vật lý</p>
                            </div>
                            <i class="fas fa-box text-4xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">Các sản phẩm hàng hoá có thể bán và giao hàng</p>
                        <a href="{{ route('mshopkeeper.inventory.index', ['itemType' => '1']) }}"
                           class="inline-block bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                            Xem Hàng Hoá <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Combo -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold">Combo</h3>
                                <p class="opacity-90">Gói sản phẩm</p>
                            </div>
                            <i class="fas fa-layer-group text-4xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">Các gói combo sản phẩm với giá ưu đãi</p>
                        <a href="{{ route('mshopkeeper.inventory.index', ['itemType' => '2']) }}"
                           class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                            Xem Combo <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Dịch Vụ -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold">Dịch Vụ</h3>
                                <p class="opacity-90">Dịch vụ cung cấp</p>
                            </div>
                            <i class="fas fa-concierge-bell text-4xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">Các dịch vụ chuyên nghiệp được cung cấp</p>
                        <a href="{{ route('mshopkeeper.inventory.index', ['itemType' => '4']) }}"
                           class="inline-block bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                            Xem Dịch Vụ <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Sản Phẩm Nổi Bật</h2>
                <p class="text-gray-600">Những sản phẩm có nhiều tồn kho và được quan tâm nhất</p>
            </div>
            
            <div class="text-center">
                <a href="{{ route('mshopkeeper.inventory.featured') }}" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors">
                    Xem Sản Phẩm Nổi Bật <i class="fas fa-star ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Thống Kê Kho Hàng</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6" id="inventory-stats">
                <div class="bg-white rounded-lg p-6 text-center shadow-md">
                    <div class="text-3xl font-bold text-blue-600 mb-2" id="total-products">-</div>
                    <div class="text-gray-600">Tổng Sản Phẩm</div>
                </div>
                <div class="bg-white rounded-lg p-6 text-center shadow-md">
                    <div class="text-3xl font-bold text-green-600 mb-2" id="in-stock">-</div>
                    <div class="text-gray-600">Còn Hàng</div>
                </div>
                <div class="bg-white rounded-lg p-6 text-center shadow-md">
                    <div class="text-3xl font-bold text-red-600 mb-2" id="out-of-stock">-</div>
                    <div class="text-gray-600">Hết Hàng</div>
                </div>
                <div class="bg-white rounded-lg p-6 text-center shadow-md">
                    <div class="text-3xl font-bold text-purple-600 mb-2" id="avg-price">-</div>
                    <div class="text-gray-600">Giá TB</div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load inventory stats
    fetch('{{ route("mshopkeeper.inventory.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-products').textContent = data.total_products.toLocaleString();
            document.getElementById('in-stock').textContent = data.total_in_stock.toLocaleString();
            document.getElementById('out-of-stock').textContent = data.total_out_of_stock.toLocaleString();
            document.getElementById('avg-price').textContent = new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(data.avg_price || 0);
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
});
</script>
@endpush
@endsection
