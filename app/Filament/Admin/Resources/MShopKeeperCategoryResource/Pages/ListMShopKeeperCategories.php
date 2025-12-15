<?php

namespace App\Filament\Admin\Resources\MShopKeeperCategoryResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperCategoryResource;
use App\Models\MShopKeeperCategory;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ListMShopKeeperCategories extends ListRecords
{
    protected static string $resource = MShopKeeperCategoryResource::class;

    /**
     * Auto sync khi vào trang nếu cần thiết
     */
    public function mount(): void
    {
        parent::mount();

        // Kiểm tra xem có cần sync không
        if ($this->shouldAutoSync()) {
            $this->performAutoSync();
        }
    }

    /**
     * Kiểm tra xem có nên auto sync không
     */
    private function shouldAutoSync(): bool
    {
        $stats = MShopKeeperCategory::getSyncStats();

        // Nếu chưa có dữ liệu gì, sync ngay
        if ($stats['total'] === 0) {
            return true;
        }

        // Nếu chưa sync lần nào, sync ngay
        if (!$stats['last_sync']) {
            return true;
        }

        // Nếu sync cuối cách đây hơn 30 phút, sync lại
        $lastSync = Carbon::parse($stats['last_sync']);
        $shouldSync = $lastSync->lt(Carbon::now()->subMinutes(30));

        return $shouldSync;
    }

    /**
     * Thực hiện auto sync
     */
    private function performAutoSync(): void
    {
        try {
            // Chạy sync command trong background
            Artisan::call('mshopkeeper:sync-categories');

            // Hiển thị notification nhẹ
            Notification::make()
                ->title('Đã cập nhật dữ liệu')
                ->body('Dữ liệu danh mục đã được đồng bộ tự động.')
                ->success()
                ->duration(3000) // 3 giây
                ->send();

        } catch (\Exception $e) {
            // Nếu có lỗi, chỉ log không hiển thị notification để không làm phiền user
            Log::warning('Auto sync failed on page load', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync_now')
                ->label('Sync ngay')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    // Gọi command sync
                    Artisan::call('mshopkeeper:sync-categories', ['--force' => true]);
                    
                    Notification::make()
                        ->title('Đã sync dữ liệu')
                        ->body('Dữ liệu danh mục đã được đồng bộ từ MShopKeeper API.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('view_tree')
                ->label('Xem dạng cây')
                ->icon('heroicon-o-squares-plus')
                ->color('secondary')
                ->modalHeading('Cây danh mục MShopKeeper')
                ->modalContent(fn (): \Illuminate\Contracts\View\View => view(
                    'filament.admin.resources.mshopkeeper-category.tree-modal'
                ))
                ->modalWidth('7xl'),

            Actions\Action::make('sync_stats')
                ->label('Thống kê sync')
                ->icon('heroicon-o-chart-bar')
                ->color('gray')
                ->modalHeading('Thống kê đồng bộ')
                ->modalContent(fn (): \Illuminate\Contracts\View\View => view(
                    'filament.admin.resources.mshopkeeper-category.sync-stats'
                )),

            Actions\Action::make('category_guide')
                ->label('Hướng dẫn')
                ->icon('heroicon-o-question-mark-circle')
                ->color('info')
                ->modalHeading('Hướng dẫn phân loại danh mục')
                ->modalContent(fn (): \Illuminate\Contracts\View\View => view(
                    'filament.admin.resources.mshopkeeper-category.category-guide'
                ))
                ->modalWidth('2xl'),
        ];
    }

    public function getTitle(): string
    {
        return 'Danh mục sản phẩm MShopKeeper';
    }

    public function getHeading(): string
    {
        return 'Danh mục sản phẩm MShopKeeper';
    }

    public function getSubheading(): ?string
    {
        $stats = MShopKeeperCategory::getSyncStats();
        $mockMode = config('mshopkeeper.mock_mode', false);
        $environment = config('mshopkeeper.environment', 'dev');

        $status = $mockMode ? 'Mock' : 'Live';
        $env = ucfirst($environment);

        // Get type distribution
        $branchCount = MShopKeeperCategory::where('is_leaf', false)->count();
        $leafCount = MShopKeeperCategory::where('is_leaf', true)->count();

        // Format last sync time
        $lastSyncText = 'Chưa sync';
        if ($stats['last_sync']) {
            $lastSyncTime = \Carbon\Carbon::parse($stats['last_sync']);
            $lastSyncText = $lastSyncTime->diffForHumans();
        }

        return "MShopKeeper API • {$status} • {$env} • {$stats['total']} danh mục • Sync: {$lastSyncText}\n" .
               "📂 {$branchCount} nhánh • 🍃 {$leafCount} lá";
    }
}
