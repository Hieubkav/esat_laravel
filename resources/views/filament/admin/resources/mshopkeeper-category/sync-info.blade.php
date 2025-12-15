<div class="space-y-6">
    <!-- API Configuration -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Cấu hình API</h3>
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Base URL:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ config('mshopkeeper.base_url') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Domain:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ config('mshopkeeper.domain') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Environment:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ config('mshopkeeper.environment') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Mock Mode:</span>
                <span class="text-sm {{ config('mshopkeeper.mock_mode') ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400' }}">
                    {{ config('mshopkeeper.mock_mode') ? 'Bật (Test)' : 'Tắt (Live)' }}
                </span>
            </div>
        </div>
    </div>

    <!-- API Endpoints -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">API Endpoints</h3>
        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600">Categories (List):</span>
                <span class="text-sm text-gray-900 font-mono">{{ config('mshopkeeper.endpoints.categories') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600">Categories (Tree):</span>
                <span class="text-sm text-gray-900 font-mono">{{ config('mshopkeeper.endpoints.categories_tree') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600">Authentication:</span>
                <span class="text-sm text-gray-900 font-mono">{{ config('mshopkeeper.endpoints.login') }}</span>
            </div>
        </div>
    </div>

    <!-- Cache Settings -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Cài đặt Cache</h3>
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Categories TTL:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ config('mshopkeeper.cache.categories_ttl') }} giây ({{ config('mshopkeeper.cache.categories_ttl') / 60 }} phút)</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Token TTL:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ config('mshopkeeper.cache.token_ttl') }} giây ({{ config('mshopkeeper.cache.token_ttl') / 60 }} phút)</span>
            </div>
        </div>
    </div>

    <!-- Data Structure -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Cấu trúc dữ liệu</h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-3">Dữ liệu danh mục từ MShopKeeper API bao gồm:</p>
            <ul class="text-sm text-gray-900 space-y-1">
                <li><strong>Id:</strong> Mã định danh duy nhất (GUID)</li>
                <li><strong>Code:</strong> Mã danh mục</li>
                <li><strong>Name:</strong> Tên danh mục</li>
                <li><strong>Description:</strong> Mô tả danh mục</li>
                <li><strong>Grade:</strong> Cấp độ trong cây danh mục</li>
                <li><strong>Inactive:</strong> Trạng thái hoạt động</li>
                <li><strong>IsLeaf:</strong> Có phải node lá không</li>
                <li><strong>ParentId:</strong> ID danh mục cha</li>
                <li><strong>SortOrder:</strong> Thứ tự sắp xếp</li>
                <li><strong>Children:</strong> Danh mục con (chỉ có trong API tree)</li>
            </ul>
        </div>
    </div>

    <!-- Sync Status -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Trạng thái đồng bộ</h3>
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800">Lưu ý quan trọng</h4>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Dữ liệu được đồng bộ real-time từ MShopKeeper API</li>
                            <li>Không thể tạo, sửa, xóa danh mục từ hệ thống này</li>
                            <li>Sử dụng cache để tối ưu hiệu suất</li>
                            <li>Dữ liệu có thể bị delay tối đa {{ config('mshopkeeper.cache.categories_ttl') }} giây</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Sync Info -->
    @php
        $domain = config('mshopkeeper.domain');
        $cacheKey = "mshopkeeper_categories_{$domain}";
        $lastSync = \Illuminate\Support\Facades\Cache::get($cacheKey . '_timestamp');
    @endphp
    
    @if($lastSync)
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Lần đồng bộ cuối</h3>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">
                            Đồng bộ thành công lúc: <strong>{{ \Carbon\Carbon::parse($lastSync)->format('d/m/Y H:i:s') }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
