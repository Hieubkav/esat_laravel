<?php

namespace App\Filament\Admin\Resources\MShopKeeperInventoryItemResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperInventoryItemResource;
use App\Services\MShopKeeperService;
use App\Models\MShopKeeperInventoryItem;
use App\Models\MShopKeeperInventoryStock;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ListMShopKeeperInventoryItems extends ListRecords
{
    protected static string $resource = MShopKeeperInventoryItemResource::class;

    public function mount(): void
    {
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sync từ API')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    try {
                        // Chỉ sync sản phẩm - NHANH HƠN (category_name đã có trong API)
                        \Filament\Notifications\Notification::make()
                            ->title('Đang sync sản phẩm...')
                            ->info()
                            ->send();

                        \Illuminate\Support\Facades\Artisan::call('mshopkeeper:sync-inventory-items', [
                            '--include-inventory' => true,
                            '--sync-by-category' => true
                        ]);

                        $output = \Illuminate\Support\Facades\Artisan::output();

                        // Parse output để lấy stats
                        preg_match('/Items Created\s*\|\s*(\d+)/', $output, $createdMatches);
                        preg_match('/Items Updated\s*\|\s*(\d+)/', $output, $updatedMatches);
                        preg_match('/Stocks Created\s*\|\s*(\d+)/', $output, $stocksCreatedMatches);

                        $created = $createdMatches[1] ?? 0;
                        $updated = $updatedMatches[1] ?? 0;
                        $stocksCreated = $stocksCreatedMatches[1] ?? 0;

                        \Filament\Notifications\Notification::make()
                            ->title('Sync hoàn tất!')
                            ->body("Hàng hóa - Tạo mới: {$created}, Cập nhật: {$updated} | Tồn kho: {$stocksCreated}")
                            ->success()
                            ->send();

                        // Refresh page để hiển thị dữ liệu mới
                        $this->redirect(request()->header('Referer'));

                    } catch (\Exception $e) {
                        Log::error('Error syncing inventory items', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Lỗi sync!')
                            ->body('Có lỗi xảy ra khi sync dữ liệu: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Xác nhận sync dữ liệu')
                ->modalDescription('Bạn có chắc chắn muốn sync dữ liệu hàng hóa từ MShopKeeper API? Quá trình này có thể mất vài phút.')
                ->modalSubmitActionLabel('Sync ngay'),

            Actions\Action::make('sync_quick')
                ->label('Sync nhanh')
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->action(function () {
                    try {
                        // Chạy sync command không bao gồm inventory
                        \Illuminate\Support\Facades\Artisan::call('mshopkeeper:sync-inventory-items');

                        $output = \Illuminate\Support\Facades\Artisan::output();

                        // Parse output để lấy stats
                        preg_match('/Items Created\s*\|\s*(\d+)/', $output, $createdMatches);
                        preg_match('/Items Updated\s*\|\s*(\d+)/', $output, $updatedMatches);

                        $created = $createdMatches[1] ?? 0;
                        $updated = $updatedMatches[1] ?? 0;

                        \Filament\Notifications\Notification::make()
                            ->title('Sync nhanh thành công!')
                            ->body("Đã tạo mới: {$created}, Cập nhật: {$updated} (không bao gồm tồn kho)")
                            ->success()
                            ->send();

                        // Refresh page để hiển thị dữ liệu mới
                        $this->redirect(request()->header('Referer'));

                    } catch (\Exception $e) {
                        Log::error('Error quick syncing inventory items', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Lỗi sync nhanh!')
                            ->body('Có lỗi xảy ra: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('stats')
                ->label('Thống kê')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->action(function () {
                    $itemStats = MShopKeeperInventoryItem::getSyncStats();
                    $stockStats = MShopKeeperInventoryStock::getSyncStats();
                    
                    $message = "📊 Thống kê hàng hóa:\n\n";
                    $message .= "🏷️ Hàng hóa:\n";
                    $message .= "• Tổng số: " . number_format($itemStats['total']) . "\n";
                    $message .= "• Đang hoạt động: " . number_format($itemStats['active']) . "\n";
                    $message .= "• Ngừng kinh doanh: " . number_format($itemStats['inactive']) . "\n";
                    $message .= "• Sản phẩm bán được: " . number_format($itemStats['child_items']) . "\n";
                    $message .= "• Mẫu mã cha: " . number_format($itemStats['parent_items']) . "\n";
                    $message .= "• Còn hàng: " . number_format($itemStats['in_stock']) . "\n";
                    $message .= "• Hết hàng: " . number_format($itemStats['out_of_stock']) . "\n\n";
                    
                    $message .= "📦 Tồn kho:\n";
                    $message .= "• Tổng bản ghi: " . number_format($stockStats['total']) . "\n";
                    $message .= "• Tổng tồn kho: " . number_format($stockStats['total_on_hand']) . "\n";
                    $message .= "• Đang đặt hàng: " . number_format($stockStats['total_ordered']) . "\n";
                    $message .= "• Giá trị kho: " . number_format($stockStats['total_stock_value']) . " VND\n\n";
                    
                    $message .= "🔄 Sync:\n";
                    $message .= "• Sync cuối: " . ($itemStats['last_sync'] ? $itemStats['last_sync']->format('d/m/Y H:i') : 'Chưa có');

                    \Filament\Notifications\Notification::make()
                        ->title('Thống kê hệ thống')
                        ->body($message)
                        ->info()
                        ->duration(15000)
                        ->send();
                }),

            Actions\Action::make('clear_cache')
                ->label('Xóa Cache')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->action(function () {
                    try {
                        // Xóa cache liên quan đến inventory
                        \Illuminate\Support\Facades\Cache::forget('mshopkeeper_inventory_*');
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Đã xóa cache!')
                            ->body('Cache dữ liệu hàng hóa đã được xóa.')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Lỗi xóa cache!')
                            ->body('Có lỗi xảy ra: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Xác nhận xóa cache')
                ->modalDescription('Bạn có chắc chắn muốn xóa cache dữ liệu hàng hóa?')
                ->modalSubmitActionLabel('Xóa cache'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Có thể thêm widgets thống kê ở đây
        ];
    }

    public function getTitle(): string
    {
        return 'Hàng hóa MShopKeeper';
    }

    public function getHeading(): string
    {
        return 'Hàng hóa MShopKeeper';
    }

    public function getSubheading(): ?string
    {
        $itemStats = MShopKeeperInventoryItem::getSyncStats();
        $stockStats = MShopKeeperInventoryStock::getSyncStats();
        
        return "Hàng hóa: " . number_format($itemStats['total']) . " | " .
               "Hoạt động: " . number_format($itemStats['active']) . " | " .
               "Tồn kho: " . number_format($stockStats['total_on_hand']) . " | " .
               "Sync cuối: " . ($itemStats['last_sync'] ? $itemStats['last_sync']->diffForHumans() : 'Chưa có');
    }
}
