<?php

namespace App\Filament\Admin\Widgets;

use App\Models\MShopKeeperInvoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class MShopKeeperInvoiceStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        // Cache stats for 5 minutes to improve performance
        $stats = Cache::remember('mshopkeeper_invoice_stats', 300, function () {
            $syncStats = MShopKeeperInvoice::getSyncStats();
            $revenueStats = MShopKeeperInvoice::getRevenueStats();
            
            return [
                'sync_stats' => $syncStats,
                'revenue_stats' => $revenueStats,
                'payment_stats' => [
                    'paid' => MShopKeeperInvoice::where('payment_status', 3)->count(),
                    'pending' => MShopKeeperInvoice::whereIn('payment_status', [1, 2, 5, 6, 10])->count(),
                    'cancelled' => MShopKeeperInvoice::where('payment_status', 4)->count(),
                ],
                'channel_stats' => [
                    'website' => MShopKeeperInvoice::where('sale_channel_name', 'Website')->count(),
                    'facebook' => MShopKeeperInvoice::where('sale_channel_name', 'Facebook')->count(),
                    'shopee' => MShopKeeperInvoice::where('sale_channel_name', 'Shopee')->count(),
                ]
            ];
        });

        return [
            Stat::make('Tổng Hóa Đơn', number_format($stats['sync_stats']['total']))
                ->description($stats['sync_stats']['synced'] . ' đã đồng bộ (' . $stats['sync_stats']['sync_rate'] . '%)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([
                    $stats['sync_stats']['synced'],
                    $stats['sync_stats']['pending'],
                    $stats['sync_stats']['errors']
                ]),

            Stat::make('Doanh Thu Hôm Nay', number_format($stats['revenue_stats']['today_revenue'], 0, ',', '.') . ' ₫')
                ->description($stats['revenue_stats']['today_orders'] . ' đơn hàng')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([
                    $stats['revenue_stats']['today_revenue'] / 1000000, // Convert to millions for chart
                    $stats['revenue_stats']['yesterday_revenue'] / 1000000,
                    $stats['revenue_stats']['week_revenue'] / 1000000,
                ]),

            Stat::make('Đã Thanh Toán', number_format($stats['payment_stats']['paid']))
                ->description($stats['payment_stats']['pending'] . ' chờ xử lý, ' . $stats['payment_stats']['cancelled'] . ' đã hủy')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($stats['payment_stats']['pending'] > 0 ? 'warning' : 'success')
                ->chart([
                    $stats['payment_stats']['paid'],
                    $stats['payment_stats']['pending'],
                    $stats['payment_stats']['cancelled']
                ]),

            Stat::make('Kênh Website', number_format($stats['channel_stats']['website']))
                ->description('FB: ' . number_format($stats['channel_stats']['facebook']) . ', Shopee: ' . number_format($stats['channel_stats']['shopee']))
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary')
                ->chart([
                    $stats['channel_stats']['website'],
                    $stats['channel_stats']['facebook'],
                    $stats['channel_stats']['shopee']
                ]),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    public static function canView(): bool
    {
        // Chỉ hiển thị khi có dữ liệu
        return MShopKeeperInvoice::count() > 0;
    }
}
