<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Danh sách hạng thẻ thành viên</h3>
        <p class="text-sm text-gray-600">Các hạng thẻ thành viên hiện có trong hệ thống MShopKeeper</p>
    </div>

    <!-- Member Levels List -->
    @if(!empty($memberLevels))
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($memberLevels as $id => $name)
                    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $name }}</h4>
                                <p class="text-sm text-gray-500">ID: {{ $id }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @switch($name)
                                        @case('Vàng')
                                            bg-yellow-100 text-yellow-800
                                            @break
                                        @case('Bạc')
                                            bg-gray-100 text-gray-800
                                            @break
                                        @case('Kim cương')
                                            bg-blue-100 text-blue-800
                                            @break
                                        @case('Bạch kim')
                                            bg-green-100 text-green-800
                                            @break
                                        @default
                                            bg-purple-100 text-purple-800
                                    @endswitch
                                ">
                                    {{ $name }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không có dữ liệu</h3>
            <p class="text-gray-600">Không thể tải danh sách hạng thẻ thành viên từ API.</p>
        </div>
    @endif

    <!-- Footer Info -->
    <div class="bg-blue-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Thông tin</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Dữ liệu hạng thẻ được đồng bộ từ API MShopKeeper và có thể thay đổi theo thời gian thực.</p>
                </div>
            </div>
        </div>
    </div>
</div>
