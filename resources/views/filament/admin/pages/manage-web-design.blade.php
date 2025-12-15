<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Quản lý nội dung trang chủ</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Chỉnh sửa tiêu đề, nội dung, hình ảnh và cấu hình hiển thị</p>
            </div>
        </div>



        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <form wire:submit="save">
                {{ $this->form }}
            </form>
        </div>


    </div>
</x-filament-panels::page>
