<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuickStats extends BaseWidget
{
    protected static ?int $sort = 9;
    protected int | string | array $columnSpan = 'full';

    // Auto refresh every 5 seconds
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        // Thống kê cơ bản
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $hotProducts = Product::where('is_hot', true)->count();
        $lowStockProducts = Product::where('stock', '<=', 10)->count();

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $todayOrders = Order::whereDate('created_at', today())->count();

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();

        // Doanh thu
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $todayRevenue = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        return [
            Stat::make('Tổng sản phẩm', $totalProducts)
                ->description("{$activeProducts} đang hoạt động")
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Sản phẩm nổi bật', $hotProducts)
                ->description('Được đánh dấu hot')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),

            Stat::make('Sắp hết hàng', $lowStockProducts)
                ->description('Tồn kho ≤ 10')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'danger' : 'success'),

            Stat::make('Tổng đơn hàng', $totalOrders)
                ->description("{$completedOrders} hoàn thành")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('Đơn chờ xử lý', $pendingOrders)
                ->description('Cần xử lý ngay')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),

            Stat::make('Đơn hôm nay', $todayOrders)
                ->description('Đơn hàng mới')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('Tổng khách hàng', $totalCustomers)
                ->description("{$activeCustomers} đang hoạt động")
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Tổng doanh thu', number_format($totalRevenue, 0, ',', '.') . ' VNĐ')
                ->description('Từ đơn hoàn thành')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
