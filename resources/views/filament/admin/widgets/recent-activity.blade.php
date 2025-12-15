<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clock class="h-5 w-5" />
                Hoạt động gần đây
            </div>
        </x-slot>

        <div class="space-y-4">
            @forelse($this->getViewData()['activities'] as $activity)
                <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ 
                            match($activity['color']) {
                                'success' => 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400',
                                'warning' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400',
                                'danger' => 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400',
                                'info' => 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400',
                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-400'
                            }
                        }}">
                            @svg($activity['icon'], 'w-4 h-4')
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                @if(isset($activity['url']))
                                    <a href="{{ $activity['url'] }}" class="hover:text-primary-600 dark:hover:text-primary-400">
                                        {{ $activity['title'] }}
                                    </a>
                                @else
                                    {{ $activity['title'] }}
                                @endif
                            </h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-2">
                                {{ $activity['time']->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            {{ $activity['description'] }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <x-heroicon-o-inbox class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">Chưa có hoạt động nào</p>
                </div>
            @endforelse
        </div>

        @if(count($this->getViewData()['activities']) > 0)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>Hiển thị {{ count($this->getViewData()['activities']) }} hoạt động gần nhất</span>
                    <span>Tự động cập nhật mỗi 15 giây</span>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
