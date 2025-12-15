@php
    $stats = \App\Models\MShopKeeperCategory::getSyncStats();
    $recentSynced = \App\Models\MShopKeeperCategory::synced()
        ->orderBy('last_synced_at', 'desc')
        ->limit(5)
        ->get();
    $recentErrors = \App\Models\MShopKeeperCategory::syncErrors()
        ->orderBy('last_synced_at', 'desc')
        ->limit(5)
        ->get();
@endphp

<div class="space-y-6">
    <!-- Overall Stats -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Thống kê tổng quan</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total'] }}</p>
                    <p class="text-sm text-blue-600 dark:text-blue-400">Tổng danh mục</p>
                </div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['synced'] }}</p>
                    <p class="text-sm text-green-600 dark:text-green-400">Đã sync</p>
                </div>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['errors'] }}</p>
                    <p class="text-sm text-red-600 dark:text-red-400">Lỗi sync</p>
                </div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
                    <p class="text-sm text-yellow-600 dark:text-yellow-400">Chờ sync</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Sync Info -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Thông tin sync cuối</h3>
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            @if($stats['last_sync'])
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            Sync thành công lần cuối
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($stats['last_sync'])->format('d/m/Y H:i:s') }}
                            ({{ \Carbon\Carbon::parse($stats['last_sync'])->diffForHumans() }})
                        </p>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Chưa có lần sync nào</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Successful Syncs -->
    @if($recentSynced->count() > 0)
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Sync thành công gần đây</h3>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="space-y-3">
                    @foreach($recentSynced as $category)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->code }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $category->last_synced_at->format('d/m H:i') }}
                                </p>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    Thành công
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Sync Errors -->
    @if($recentErrors->count() > 0)
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Lỗi sync gần đây</h3>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="space-y-3">
                    @foreach($recentErrors as $category)
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->code }}</p>
                                @if($category->sync_error)
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $category->sync_error }}</p>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $category->last_synced_at->format('d/m H:i') }}
                                </p>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                    Lỗi
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Sync Commands -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Lệnh sync</h3>
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="space-y-2">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Sync thường:</p>
                    <code class="text-xs bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">
                        php artisan mshopkeeper:sync-categories
                    </code>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Force sync:</p>
                    <code class="text-xs bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">
                        php artisan mshopkeeper:sync-categories --force
                    </code>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Dry run:</p>
                    <code class="text-xs bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">
                        php artisan mshopkeeper:sync-categories --dry-run
                    </code>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Clear và sync:</p>
                    <code class="text-xs bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">
                        php artisan mshopkeeper:sync-categories --clear
                    </code>
                </div>
            </div>
        </div>
    </div>

    <!-- Cache Settings -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Cài đặt cache</h3>
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-300">Categories TTL:</span>
                    <p class="text-gray-900 dark:text-white">{{ config('mshopkeeper.cache.categories_ttl') }} giây ({{ config('mshopkeeper.cache.categories_ttl') / 60 }} phút)</p>
                </div>
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-300">Token TTL:</span>
                    <p class="text-gray-900 dark:text-white">{{ config('mshopkeeper.cache.token_ttl') }} giây ({{ config('mshopkeeper.cache.token_ttl') / 60 }} phút)</p>
                </div>
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-300">Environment:</span>
                    <p class="text-gray-900 dark:text-white">{{ config('mshopkeeper.environment') }}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-300">Mock Mode:</span>
                    <p class="text-gray-900 dark:text-white">{{ config('mshopkeeper.mock_mode') ? 'Bật' : 'Tắt' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
