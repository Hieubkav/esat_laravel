<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold">Hướng dẫn phân loại danh mục</h2>
                <p class="text-blue-100 text-sm">Hiểu rõ về cấu trúc phân cấp danh mục MShopKeeper</p>
            </div>
        </div>
    </div>

    <!-- Category Types -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Branch Categories -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                    <span class="text-xl">📂</span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Danh mục Nhánh</h3>
                    <p class="text-sm text-blue-600 dark:text-blue-300">Branch Categories</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-100 dark:border-blue-800">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Đặc điểm:</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                        <li>• <strong>Có danh mục con</strong> bên trong</li>
                        <li>• Đóng vai trò là <strong>danh mục cha</strong></li>
                        <li>• Dùng để <strong>nhóm các danh mục</strong> liên quan</li>
                        <li>• Không chứa sản phẩm trực tiếp</li>
                    </ul>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-100 dark:border-blue-800">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Ví dụ:</h4>
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        <div class="font-medium text-blue-600 dark:text-blue-400">📂 Bánh ngọt</div>
                        <div class="ml-4 mt-1 space-y-1">
                            <div>├── 📂 Bánh kem</div>
                            <div>├── 📂 Bánh quy</div>
                            <div>└── 📂 Bánh su kem</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaf Categories -->
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🍃</span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-green-900 dark:text-green-100">Danh mục Lá</h3>
                    <p class="text-sm text-green-600 dark:text-green-300">Leaf Categories</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-green-100 dark:border-green-800">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Đặc điểm:</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                        <li>• <strong>Không có danh mục con</strong></li>
                        <li>• Là <strong>danh mục cuối cùng</strong> trong cây</li>
                        <li>• <strong>Chứa sản phẩm</strong> trực tiếp</li>
                        <li>• Không thể mở rộng thêm</li>
                    </ul>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-green-100 dark:border-green-800">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Ví dụ:</h4>
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        <div class="font-medium text-gray-500 dark:text-gray-400">📂 Bánh kem</div>
                        <div class="ml-4 mt-1 space-y-1">
                            <div class="text-green-600 dark:text-green-400">├── 🍃 Bánh kem chocolate</div>
                            <div class="text-green-600 dark:text-green-400">├── 🍃 Bánh kem vanilla</div>
                            <div class="text-green-600 dark:text-green-400">└── 🍃 Bánh kem strawberry</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    @php
        $branchCount = \App\Models\MShopKeeperCategory::where('is_leaf', false)->count();
        $leafCount = \App\Models\MShopKeeperCategory::where('is_leaf', true)->count();
        $totalCount = \App\Models\MShopKeeperCategory::count();
    @endphp

    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📊 Thống kê hiện tại</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-700 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $branchCount }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-300">📂 Danh mục Nhánh</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ round($branchCount / $totalCount * 100, 1) }}% tổng số</div>
            </div>
            
            <div class="bg-white dark:bg-gray-700 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $leafCount }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-300">🍃 Danh mục Lá</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ round($leafCount / $totalCount * 100, 1) }}% tổng số</div>
            </div>
            
            <div class="bg-white dark:bg-gray-700 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $totalCount }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-300">📋 Tổng danh mục</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">100% hệ thống</div>
            </div>
        </div>
    </div>

    <!-- How it works -->
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-6">
        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-amber-100 dark:bg-amber-800 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-amber-900 dark:text-amber-100 mb-2">💡 Cách hệ thống xác định</h3>
                <div class="text-sm text-amber-800 dark:text-amber-200 space-y-2">
                    <p><strong>Tự động phân loại:</strong> Hệ thống tự động xác định loại danh mục dựa trên cấu trúc dữ liệu từ MShopKeeper API.</p>
                    <p><strong>Nhánh:</strong> Danh mục có ít nhất 1 danh mục con bên trong.</p>
                    <p><strong>Lá:</strong> Danh mục không có danh mục con nào (children_count = 0).</p>
                    <p><strong>Cập nhật:</strong> Trạng thái được cập nhật tự động mỗi khi sync dữ liệu từ API.</p>
                </div>
            </div>
        </div>
    </div>
</div>
