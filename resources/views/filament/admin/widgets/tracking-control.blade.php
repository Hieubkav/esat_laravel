<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            🎛️ Bảng Điều Khiển Tracking
        </x-slot>

        <x-slot name="description">
            Quản lý và theo dõi dữ liệu tracking realtime. Sử dụng các nút ở góc trên bên phải để reset dữ liệu test.
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Website Visitors -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100">🌐 Lượt Truy Cập</h3>
                        <div class="mt-2">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalVisitors) }}</div>
                            <div class="text-xs text-blue-500 dark:text-blue-300">Tổng cộng</div>
                        </div>
                        <div class="mt-1">
                            <div class="text-lg font-semibold text-blue-700 dark:text-blue-300">{{ number_format($todayVisitors) }}</div>
                            <div class="text-xs text-blue-500 dark:text-blue-300">Hôm nay</div>
                        </div>
                    </div>
                    <div class="text-3xl opacity-50">🌐</div>
                </div>
            </div>

            <!-- Post Views -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-green-900 dark:text-green-100">📝 Lượt Xem Bài Viết</h3>
                        <div class="mt-2">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalPostViews) }}</div>
                            <div class="text-xs text-green-500 dark:text-green-300">Tổng cộng</div>
                        </div>
                        <div class="mt-1">
                            <div class="text-lg font-semibold text-green-700 dark:text-green-300">{{ number_format($todayPostViews) }}</div>
                            <div class="text-xs text-green-500 dark:text-green-300">Hôm nay</div>
                        </div>
                    </div>
                    <div class="text-3xl opacity-50">📝</div>
                </div>
            </div>

            <!-- Product Views -->
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-purple-900 dark:text-purple-100">🛍️ Lượt Xem Sản Phẩm</h3>
                        <div class="mt-2">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($totalProductViews) }}</div>
                            <div class="text-xs text-purple-500 dark:text-purple-300">Tổng cộng</div>
                        </div>
                        <div class="mt-1">
                            <div class="text-lg font-semibold text-purple-700 dark:text-purple-300">{{ number_format($todayProductViews) }}</div>
                            <div class="text-xs text-purple-500 dark:text-purple-300">Hôm nay</div>
                        </div>
                    </div>
                    <div class="text-3xl opacity-50">🛍️</div>
                </div>
            </div>
        </div>

        <!-- Reset Buttons -->
        <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
            <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-3">🎛️ Công Cụ Reset Tracking</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Reset Visitors Button -->
                <button
                    wire:click="resetVisitors"
                    wire:confirm="Bạn có chắc chắn muốn xóa tất cả dữ liệu tracking lượt truy cập website? Hành động này không thể hoàn tác."
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors duration-200 text-sm font-medium"
                >
                    🌐 Reset Lượt Truy Cập
                </button>

                <!-- Reset Content Views Button -->
                <button
                    wire:click="resetContentViews"
                    wire:confirm="Bạn có chắc chắn muốn xóa tất cả dữ liệu tracking lượt xem bài viết và sản phẩm?"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 text-sm font-medium"
                >
                    📄 Reset Lượt Xem
                </button>

                <!-- Reset All Button -->
                <button
                    wire:click="resetAllTracking"
                    wire:confirm="Bạn có chắc chắn muốn xóa TẤT CẢ dữ liệu tracking? Hành động này không thể hoàn tác!"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200 text-sm font-medium"
                >
                    🗑️ Reset Tất Cả
                </button>

                <!-- Generate Test Data Button -->
                <button
                    wire:click="generateTestData"
                    wire:confirm="Bạn có muốn tạo 30 bản ghi dữ liệu test để kiểm tra hệ thống tracking?"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-200 text-sm font-medium"
                >
                    🧪 Tạo Dữ Liệu Test
                </button>
            </div>

            <!-- Instructions -->
            <div class="mt-4 p-3 bg-white/50 dark:bg-gray-800/50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-600 dark:text-gray-400">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            <strong>Reset Lượt Truy Cập:</strong> Xóa dữ liệu tracking website
                        </div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <strong>Reset Lượt Xem:</strong> Xóa dữ liệu tracking bài viết & sản phẩm
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            <strong>Reset Tất Cả:</strong> Xóa toàn bộ dữ liệu tracking
                        </div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <strong>Tạo Dữ Liệu Test:</strong> Tạo dữ liệu mẫu để kiểm tra
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
