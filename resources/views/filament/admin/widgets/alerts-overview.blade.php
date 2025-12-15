<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bell class="h-5 w-5" />
                Cảnh báo & Thông tin quan trọng
            </div>
        </x-slot>

        <div class="space-y-3">
            @foreach($this->getViewData()['alerts'] as $alert)
                <div class="alert-item flex items-start gap-3 p-4 rounded-lg border-l-4 {{
                    match($alert['color']) {
                        'danger' => 'border-red-500 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30',
                        'warning' => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30',
                        'success' => 'border-green-500 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30',
                        'info' => 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30',
                        default => 'border-gray-500 bg-gray-50 dark:bg-gray-900/20 hover:bg-gray-100 dark:hover:bg-gray-900/30'
                    }
                }} transition-all duration-200 hover:shadow-md dark:hover:shadow-lg">

                    <div class="flex-shrink-0 mt-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{
                            match($alert['color']) {
                                'danger' => 'bg-red-100 text-red-600 dark:bg-red-800/50 dark:text-red-400',
                                'warning' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-800/50 dark:text-yellow-400',
                                'success' => 'bg-green-100 text-green-600 dark:bg-green-800/50 dark:text-green-400',
                                'info' => 'bg-blue-100 text-blue-600 dark:bg-blue-800/50 dark:text-blue-400',
                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-800/50 dark:text-gray-400'
                            }
                        }} transition-colors duration-200">
                            @svg($alert['icon'], 'w-4 h-4')
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                            {{ $alert['title'] }}
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                            {{ $alert['description'] }}
                        </p>
                        @if($alert['action'])
                            <button class="text-xs font-medium {{
                                match($alert['color']) {
                                    'danger' => 'text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300',
                                    'warning' => 'text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300',
                                    'success' => 'text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300',
                                    'info' => 'text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300',
                                    default => 'text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
                                }
                            }} transition-colors duration-200 hover:underline">
                                {{ $alert['action'] }} →
                            </button>
                        @endif
                    </div>

                    @if($alert['type'] === 'urgent')
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>Cập nhật mỗi 15 giây</span>
                <span>{{ now()->format('H:i:s d/m/Y') }}</span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
