<?php

namespace App\Filament\Admin\Resources\MShopKeeperInvoiceResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperInvoiceResource;
use App\Models\MShopKeeperInvoice;
use App\Jobs\SyncMShopKeeperInvoicesJob;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;

class ListMShopKeeperInvoices extends ListRecords
{
    protected static string $resource = MShopKeeperInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_now')
                ->label('Đồng bộ ngay')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->size(ActionSize::Small)
                ->action(function () {
                    try {
                        // Dispatch sync job
                        SyncMShopKeeperInvoicesJob::dispatchWeeklySync();

                        Notification::make()
                            ->title('Đồng bộ đã được lên lịch!')
                            ->body('Đang đồng bộ hóa đơn từ MShopKeeper trong background. Vui lòng chờ vài phút.')
                            ->info()
                            ->send();

                        // Refresh trang sau 3 giây
                        $this->redirect(request()->header('Referer'));

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Lỗi lên lịch đồng bộ')
                            ->body('Có lỗi xảy ra: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Xác nhận đồng bộ')
                ->modalDescription('Bạn có chắc muốn đồng bộ hóa đơn từ MShopKeeper? Quá trình này có thể mất vài phút.')
                ->modalSubmitActionLabel('Đồng bộ'),

            Action::make('sync_stats')
                ->label('Thống kê')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->size(ActionSize::Small)
                ->action(function () {
                    $stats = MShopKeeperInvoice::getSyncStats();
                    $revenueStats = MShopKeeperInvoice::getRevenueStats();

                    $message = "📊 **Thống kê đồng bộ:**\n";
                    $message .= "• Tổng: {$stats['total']} hóa đơn\n";
                    $message .= "• Đã đồng bộ: {$stats['synced']} ({$stats['sync_rate']}%)\n";
                    $message .= "• Lỗi: {$stats['errors']}\n";
                    $message .= "• Chờ xử lý: {$stats['pending']}\n\n";

                    $message .= "💰 **Doanh thu hôm nay:**\n";
                    $message .= "• Đơn hàng: {$revenueStats['today_orders']}\n";
                    $message .= "• Doanh thu: " . number_format($revenueStats['today_revenue'], 0, ',', '.') . " ₫\n\n";

                    if ($stats['last_sync']) {
                        $message .= "🕒 **Sync cuối:** " . $stats['last_sync']->format('d/m/Y H:i');
                    }

                    Notification::make()
                        ->title('Thống kê hóa đơn MShopKeeper')
                        ->body($message)
                        ->info()
                        ->duration(10000)
                        ->send();
                }),

            Action::make('invoice_guide')
                ->label('Hướng dẫn')
                ->icon('heroicon-o-question-mark-circle')
                ->color('gray')
                ->size(ActionSize::Small)
                ->action(function () {
                    $guide = "📖 **Hướng dẫn sử dụng:**\n\n";
                    $guide .= "🔄 **Đồng bộ tự động:** Hệ thống tự động đồng bộ 3 lần/ngày (9:45, 13:45, 17:45)\n\n";
                    $guide .= "🔍 **Bộ lọc:**\n";
                    $guide .= "• Lọc theo trạng thái thanh toán\n";
                    $guide .= "• Lọc theo kênh bán hàng\n";
                    $guide .= "• Lọc đơn từ Website\n";
                    $guide .= "• Lọc theo ngày tạo\n\n";
                    $guide .= "📊 **Trạng thái đồng bộ:**\n";
                    $guide .= "• 🟢 Đã đồng bộ: Dữ liệu đã cập nhật\n";
                    $guide .= "• 🟡 Chờ xử lý: Đang chờ đồng bộ\n";
                    $guide .= "• 🔴 Lỗi: Có lỗi trong quá trình đồng bộ\n\n";
                    $guide .= "⚡ **Tự động refresh:** Trang tự động làm mới mỗi 30 giây";

                    Notification::make()
                        ->title('Hướng dẫn sử dụng')
                        ->body($guide)
                        ->info()
                        ->duration(15000)
                        ->send();
                }),
        ];
    }

    /**
     * Auto-sync khi load trang (nếu chưa sync trong 1 giờ qua)
     */
    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getTableQuery();

        // Kiểm tra xem có cần auto-sync không
        $lastSync = MShopKeeperInvoice::whereNotNull('last_synced_at')
            ->orderBy('last_synced_at', 'desc')
            ->value('last_synced_at');

        if (!$lastSync || $lastSync->diffInHours(now()) > 1) {
            // Dispatch sync job nếu cần
            try {
                SyncMShopKeeperInvoicesJob::dispatchDailySync();
            } catch (\Exception) {
                // Ignore errors in background sync
            }
        }

        return $query;
    }

    /**
     * Subheading hiển thị thống kê chi tiết như các module MShopKeeper khác
     */
    public function getSubheading(): ?string
    {
        try {
            $stats = MShopKeeperInvoice::getSyncStats();
            $revenueStats = MShopKeeperInvoice::getRevenueStats();

            // Đếm số hóa đơn theo trạng thái thanh toán
            $paidCount = MShopKeeperInvoice::where('payment_status', 3)->count(); // Đã thanh toán
            $pendingCount = MShopKeeperInvoice::whereIn('payment_status', [1, 2, 5, 6, 10])->count(); // Chưa thanh toán/đang xử lý

            return "Hóa đơn: " . number_format($stats['total']) . " | " .
                   "Đã thanh toán: " . number_format($paidCount) . " | " .
                   "Chờ xử lý: " . number_format($pendingCount) . " | " .
                   "Doanh thu hôm nay: " . number_format($revenueStats['today_revenue'], 0, ',', '.') . " ₫ | " .
                   "Sync cuối: " . ($stats['last_sync'] ? $stats['last_sync']->diffForHumans() : 'Chưa có');
        } catch (\Exception) {
            return "Đang tải thống kê...";
        }
    }
}
