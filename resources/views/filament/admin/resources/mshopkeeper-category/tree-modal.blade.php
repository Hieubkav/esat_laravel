@php
    $treeData = \App\Models\MShopKeeperCategory::getFullTree();
    $totalCategories = \App\Models\MShopKeeperCategory::count();
    $rootCategories = \App\Models\MShopKeeperCategory::root()->count();
    $maxDepth = \App\Models\MShopKeeperCategory::max('grade') ?? 0;
    $activeCategories = \App\Models\MShopKeeperCategory::active()->count();
    $leafCategories = \App\Models\MShopKeeperCategory::leaf()->count();
@endphp

<div class="space-y-6">
    <!-- Header with gradient -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">🌳 Cây danh mục MShopKeeper</h2>
                <p class="text-blue-100">Cấu trúc phân cấp danh mục sản phẩm từ hệ thống MShopKeeper</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ $totalCategories }}</div>
                <div class="text-sm text-blue-100">Tổng danh mục</div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total Categories -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Tổng số</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $totalCategories }}</p>
                </div>
                <div class="p-3 bg-blue-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7l-7 7-7-7m14 18l-7-7-7 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Root Categories -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-4 border border-green-200 dark:border-green-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Danh mục gốc</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $rootCategories }}</p>
                </div>
                <div class="p-3 bg-green-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Categories -->
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Đang hoạt động</p>
                    <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $activeCategories }}</p>
                </div>
                <div class="p-3 bg-emerald-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Max Depth -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-4 border border-purple-200 dark:border-purple-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Độ sâu tối đa</p>
                    <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $maxDepth }} <span class="text-sm font-normal">cấp</span></p>
                </div>
                <div class="p-3 bg-purple-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tree Structure -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <!-- Tree Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cấu trúc cây danh mục</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Hiển thị theo thứ tự phân cấp</p>
                    </div>
                </div>

                <!-- Tree Controls -->
                <div class="flex items-center space-x-3">
                    <button onclick="expandAll()" class="px-4 py-2 text-xs font-medium text-blue-600 dark:text-blue-400 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/10 rounded-xl hover:from-blue-100 hover:to-cyan-100 dark:hover:from-blue-900/40 dark:hover:to-cyan-900/20 transition-all duration-200 border border-blue-200/50 dark:border-blue-700/30 hover:shadow-md hover:scale-105">
                        <svg class="w-3 h-3 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Mở rộng tất cả
                    </button>
                    <button onclick="collapseAll()" class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-700 dark:to-slate-700 rounded-xl hover:from-gray-100 hover:to-slate-100 dark:hover:from-gray-600 dark:hover:to-slate-600 transition-all duration-200 border border-gray-200/50 dark:border-gray-600/30 hover:shadow-md hover:scale-105">
                        <svg class="w-3 h-3 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                        </svg>
                        Thu gọn tất cả
                    </button>
                </div>
            </div>
        </div>

        <!-- Tree Content -->
        <div class="p-6">


            @if(count($treeData) > 0)
                <div class="space-y-1 max-h-[500px] overflow-y-auto custom-scrollbar">
                    @foreach($treeData as $node)
                        @include('filament.admin.resources.mshopkeeper-category.tree-node', ['node' => $node, 'level' => 0])
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Không có danh mục nào</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                        Chưa có dữ liệu danh mục trong database. Hãy chạy sync để tải dữ liệu từ MShopKeeper API.
                    </p>
                    <button class="mt-4 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Sync dữ liệu ngay
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #374151;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #6b7280;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>

<!-- JavaScript for Tree Controls -->
<script>
    function expandAll() {
        document.querySelectorAll('.tree-node-children').forEach(el => {
            el.style.display = 'block';
        });
        document.querySelectorAll('.tree-toggle-icon').forEach(el => {
            el.style.transform = 'rotate(90deg)';
        });
    }

    function collapseAll() {
        document.querySelectorAll('.tree-node-children').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.tree-toggle-icon').forEach(el => {
            el.style.transform = 'rotate(0deg)';
        });
    }

    function toggleNode(nodeId) {
        const children = document.getElementById('children-' + nodeId);
        const icon = document.getElementById('icon-' + nodeId);

        if (children.style.display === 'none' || children.style.display === '') {
            children.style.display = 'block';
            icon.style.transform = 'rotate(90deg)';
        } else {
            children.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }
</script>
