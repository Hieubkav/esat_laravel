<?php

namespace App\Filament\Admin\Resources\MShopKeeperCustomerPointResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperCustomerPointResource;
use App\Services\MShopKeeperService;
use App\Models\MShopKeeperCustomerPoint;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ListMShopKeeperCustomerPoints extends ListRecords
{
    protected static string $resource = MShopKeeperCustomerPointResource::class;

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
                        // Chạy sync command
                        \Illuminate\Support\Facades\Artisan::call('mshopkeeper:sync-customer-points');

                        $output = \Illuminate\Support\Facades\Artisan::output();

                        // Parse output để lấy stats
                        preg_match('/Created\s*\|\s*(\d+)/', $output, $createdMatches);
                        preg_match('/Updated\s*\|\s*(\d+)/', $output, $updatedMatches);

                        $created = $createdMatches[1] ?? 0;
                        $updated = $updatedMatches[1] ?? 0;

                        \Filament\Notifications\Notification::make()
                            ->title('Sync thành công!')
                            ->body("Đã tạo mới: {$created}, Cập nhật: {$updated}")
                            ->success()
                            ->send();

                        // Refresh page để hiển thị dữ liệu mới
                        $this->redirect(request()->header('Referer'));

                    } catch (\Exception $e) {
                        Log::error('Error syncing customer points', [
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
                ->modalDescription('Bạn có chắc chắn muốn sync dữ liệu điểm thẻ thành viên từ MShopKeeper API?')
                ->modalSubmitActionLabel('Sync ngay'),

            Actions\Action::make('stats')
                ->label('Thống kê')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->action(function () {
                    $stats = MShopKeeperCustomerPoint::getSyncStats();
                    
                    $message = "📊 Thống kê điểm thẻ thành viên:\n\n";
                    $message .= "• Tổng số khách hàng: " . number_format($stats['total']) . "\n";
                    $message .= "• Đã sync: " . number_format($stats['synced']) . "\n";
                    $message .= "• Lỗi: " . number_format($stats['errors']) . "\n";
                    $message .= "• Chờ sync: " . number_format($stats['pending']) . "\n";
                    $message .= "• Tổng điểm: " . number_format($stats['total_points']) . "\n";
                    $message .= "• Điểm trung bình: " . number_format($stats['avg_points'], 0) . "\n";
                    $message .= "• Sync cuối: " . ($stats['last_sync'] ? $stats['last_sync']->format('d/m/Y H:i') : 'Chưa có');

                    \Filament\Notifications\Notification::make()
                        ->title('Thống kê hệ thống')
                        ->body($message)
                        ->info()
                        ->duration(10000)
                        ->send();
                }),

            Actions\Action::make('clear_cache')
                ->label('Xóa Cache')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->action(function () {
                    try {
                        // Xóa cache liên quan đến customer points
                        \Illuminate\Support\Facades\Cache::forget('mshopkeeper_customers_point_paging_*');
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Đã xóa cache!')
                            ->body('Cache dữ liệu điểm thẻ thành viên đã được xóa.')
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
                ->modalDescription('Bạn có chắc chắn muốn xóa cache dữ liệu điểm thẻ thành viên?')
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
        return 'Điểm thẻ thành viên MShopKeeper';
    }

    public function getHeading(): string
    {
        return 'Điểm thẻ thành viên MShopKeeper';
    }

    public function getSubheading(): ?string
    {
        $stats = MShopKeeperCustomerPoint::getSyncStats();
        
        return "Tổng: " . number_format($stats['total']) . " khách hàng | " .
               "Tổng điểm: " . number_format($stats['total_points']) . " | " .
               "Sync cuối: " . ($stats['last_sync'] ? $stats['last_sync']->diffForHumans() : 'Chưa có');
    }
}
