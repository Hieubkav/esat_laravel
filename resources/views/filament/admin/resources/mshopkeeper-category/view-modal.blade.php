<div class="space-y-6">
    <!-- Basic Information -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Thông tin cơ bản</h3>
        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4 space-y-3">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Tên danh mục:</span>
                    <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $record->name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Mã danh mục:</span>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $record->code ?: '—' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">MShopKeeper ID:</span>
                    <p class="text-sm text-gray-900 dark:text-gray-100 font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $record->mshopkeeper_id }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Database ID:</span>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $record->id }}</p>
                </div>
            </div>
            
            @if($record->description)
                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Mô tả:</span>
                    <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 p-3 rounded-md">{{ $record->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Category Properties -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Thuộc tính</h3>
        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Cấp độ:</span>
                    <span class="ml-2 inline-flex px-3 py-1.5 text-xs font-semibold rounded-full {{
                        match($record->grade) {
                            0 => 'bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 border border-blue-200 dark:border-blue-600',
                            1 => 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 border border-green-200 dark:border-green-600',
                            2 => 'bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 border border-yellow-200 dark:border-yellow-600',
                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-600'
                        }
                    }}">
                        Cấp {{ $record->grade }}
                    </span>
                </div>

                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Trạng thái:</span>
                    <span class="ml-2 inline-flex px-3 py-1.5 text-xs font-semibold rounded-full {{
                        $record->inactive
                            ? 'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 border border-red-200 dark:border-red-600'
                            : 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 border border-green-200 dark:border-green-600'
                    }}">
                        {{ $record->status }}
                    </span>
                </div>

                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Loại:</span>
                    <span class="ml-2 inline-flex px-3 py-1.5 text-xs font-semibold rounded-full {{
                        $record->is_leaf
                            ? 'bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 border border-blue-200 dark:border-blue-600'
                            : 'bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-100 border border-purple-200 dark:border-purple-600'
                    }}">
                        {{ $record->type }}
                    </span>
                </div>

                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Thứ tự:</span>
                    <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded inline-block">{{ $record->sort_order }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hierarchy Information -->
    @if($record->parent || $record->children->count() > 0)
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Cấu trúc phân cấp</h3>
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4 space-y-3">
                @if($record->parent)
                    <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-md border border-blue-200 dark:border-blue-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Danh mục cha:</span>
                        <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $record->parent->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-300 font-mono">ID: {{ $record->parent->mshopkeeper_id }}</p>
                    </div>
                @endif

                @if($record->full_path)
                    <div class="bg-indigo-50 dark:bg-indigo-900/30 p-3 rounded-md border border-indigo-200 dark:border-indigo-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Đường dẫn đầy đủ:</span>
                        <p class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $record->full_path }}</p>
                    </div>
                @endif
                
                @if($record->children->count() > 0)
                    <div class="bg-green-50 dark:bg-green-900/30 p-3 rounded-md border border-green-200 dark:border-green-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Danh mục con ({{ $record->children->count() }}):</span>
                        <div class="mt-2 space-y-2">
                            @foreach($record->children->take(5) as $child)
                                <div class="text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-600">
                                    <span class="font-medium">• {{ $child->name }}</span>
                                    @if($child->code)
                                        <span class="text-xs text-gray-500 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded ml-2">({{ $child->code }})</span>
                                    @endif
                                </div>
                            @endforeach
                            @if($record->children->count() > 5)
                                <div class="text-xs text-gray-500 dark:text-gray-300 italic">
                                    ... và {{ $record->children->count() - 5 }} danh mục khác
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Sync Information -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Thông tin đồng bộ</h3>
        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4 space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-gray-700 p-3 rounded-md border border-gray-200 dark:border-gray-600">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Trạng thái sync:</span>
                    <div class="mt-1">
                        <span class="inline-flex px-3 py-1.5 text-xs font-semibold rounded-full {{
                            match($record->sync_status) {
                                'synced' => 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 border border-green-200 dark:border-green-600',
                                'error' => 'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 border border-red-200 dark:border-red-600',
                                'pending' => 'bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 border border-yellow-200 dark:border-yellow-600',
                                default => 'bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-500'
                            }
                        }}">
                            {{ ucfirst($record->sync_status) }}
                        </span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-700 p-3 rounded-md border border-gray-200 dark:border-gray-600">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Sync cuối:</span>
                    <p class="text-sm text-gray-900 dark:text-gray-100 mt-1 font-mono">{{ $record->time_since_last_sync }}</p>
                </div>
            </div>
            
            @if($record->last_synced_at)
                <div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Thời gian sync:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $record->last_synced_at->format('d/m/Y H:i:s') }}</p>
                </div>
            @endif
            
            @if($record->sync_error)
                <div>
                    <span class="text-sm font-medium text-red-600 dark:text-red-400">Lỗi sync:</span>
                    <p class="text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20 p-2 rounded mt-1">{{ $record->sync_error }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Raw Data (for debugging) -->
    @if($record->raw_data && config('app.debug'))
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Dữ liệu thô (Debug)</h3>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-auto max-h-40">{{ json_encode($record->raw_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

    <!-- Timestamps -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Thời gian</h3>
        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-white dark:bg-gray-700 p-3 rounded-md border border-gray-200 dark:border-gray-600">
                    <span class="font-medium text-gray-600 dark:text-gray-300">Tạo lúc:</span>
                    <p class="text-gray-900 dark:text-gray-100 font-mono mt-1">{{ $record->created_at->format('d/m/Y H:i:s') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-300">{{ $record->created_at->diffForHumans() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-700 p-3 rounded-md border border-gray-200 dark:border-gray-600">
                    <span class="font-medium text-gray-600 dark:text-gray-300">Cập nhật lúc:</span>
                    <p class="text-gray-900 dark:text-gray-100 font-mono mt-1">{{ $record->updated_at->format('d/m/Y H:i:s') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-300">{{ $record->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
