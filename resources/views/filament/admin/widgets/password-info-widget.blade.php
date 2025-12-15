@if($isAdmin)
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-key class="h-5 w-5 text-warning-500" />
                Thông tin mật khẩu người dùng
            </div>
        </x-slot>

        <div class="space-y-4">
            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <div class="flex items-start gap-2">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                    <div class="text-sm">
                        <p class="font-medium text-yellow-800 dark:text-yellow-200">Lưu ý bảo mật:</p>
                        <p class="text-yellow-700 dark:text-yellow-300 mt-1">
                            Thông tin mật khẩu chỉ hiển thị cho Quản trị viên. Vui lòng bảo mật thông tin này.
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-3 font-medium text-gray-900 dark:text-white">Tên</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-900 dark:text-white">Email</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-900 dark:text-white">Vai trò</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-900 dark:text-white">Mật khẩu</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-900 dark:text-white">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2 px-3 text-gray-900 dark:text-white">{{ $user->name }}</td>
                                <td class="py-2 px-3 text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="py-2 px-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $user->role === 'admin' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                        {{ $user->role === 'admin' ? 'Quản trị viên' : 'Quản lý bài viết' }}
                                    </span>
                                </td>
                                <td class="py-2 px-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-mono text-xs">
                                        {{ $user->plain_password ?: 'password' }}
                                    </span>
                                </td>
                                <td class="py-2 px-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $user->status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400">
                <p>💡 <strong>Mẹo:</strong> Sử dụng lệnh <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">php artisan user:reset-password email@example.com newpassword</code> để đặt lại mật khẩu qua CLI.</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
@endif
