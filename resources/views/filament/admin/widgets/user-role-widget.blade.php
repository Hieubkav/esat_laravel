<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-shield-check class="h-5 w-5 text-primary-500" />
                Thông tin quyền truy cập
            </div>
        </x-slot>

        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">
                        Xin chào, {{ $user->name }}!
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Vai trò hiện tại: <span class="font-medium text-primary-600 dark:text-primary-400">{{ $roleName }}</span>
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        {{ $user->role === 'admin' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                        {{ $user->role === 'admin' ? 'Quản trị viên' : 'Quản lý bài viết' }}
                    </span>
                </div>
            </div>

            <div>
                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Quyền truy cập của bạn:</h4>
                <ul class="space-y-2">
                    @foreach($permissions as $permission)
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-check-circle class="h-4 w-4 text-green-500 flex-shrink-0" />
                            {{ $permission }}
                        </li>
                    @endforeach
                </ul>
            </div>

            @if($user->role === 'post_manager')
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <div class="flex items-start gap-2">
                        <x-heroicon-o-information-circle class="h-5 w-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                        <div class="text-sm">
                            <p class="font-medium text-yellow-800 dark:text-yellow-200">Lưu ý quan trọng:</p>
                            <p class="text-yellow-700 dark:text-yellow-300 mt-1">
                                Với vai trò Quản lý bài viết, bạn chỉ có thể truy cập các chức năng quản lý bài viết và chuyên mục.
                                Nếu cần quyền truy cập khác, vui lòng liên hệ Quản trị viên.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
