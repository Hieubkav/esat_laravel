<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RealtimeStats extends BaseWidget
{
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'full';

    // Auto refresh every 5 seconds for realtime effect
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        // Thống kê hôm nay
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');
        
        // Thống kê hôm qua để so sánh
        $yesterdayOrders = Order::whereDate('created_at', today()->subDay())->count();
        $yesterdayRevenue = Order::where('status', 'completed')
            ->whereDate('created_at', today()->subDay())
            ->sum('total');

        // Thống kê tổng
        $totalProducts = Product::where('status', 'active')->count();
        $totalCustomers = Customer::where('status', 'active')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $lowStockProducts = Product::where('stock', '<=', 10)->count();

        return [
            Stat::make('Đơn hàng hôm nay', $todayOrders)
                ->description($this->getOrdersChange($todayOrders, $yesterdayOrders))
                ->descriptionIcon($this->getChangeIcon($todayOrders, $yesterdayOrders))
                ->color($this->getChangeColor($todayOrders, $yesterdayOrders))
                ->chart($this->getOrdersChart()),

            Stat::make('Doanh thu hôm nay', number_format($todayRevenue, 0, ',', '.') . ' VNĐ')
                ->description($this->getRevenueChange($todayRevenue, $yesterdayRevenue))
                ->descriptionIcon($this->getChangeIcon($todayRevenue, $yesterdayRevenue))
                ->color($this->getChangeColor($todayRevenue, $yesterdayRevenue))
                ->chart($this->getRevenueChart()),

            Stat::make('Đơn chờ xử lý', $pendingOrders)
                ->description('Cần xử lý ngay')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),

            Stat::make('Sản phẩm sắp hết', $lowStockProducts)
                ->description('Tồn kho ≤ 10')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'danger' : 'success'),

            Stat::make('Tổng sản phẩm', $totalProducts)
                ->description('Đang hoạt động')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Tổng khách hàng', $totalCustomers)
                ->description('Đã đăng ký')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }

    private function getOrdersChange($today, $yesterday): string
    {
        if ($yesterday == 0) {
            return $today > 0 ? 'Tăng 100%' : 'Không có đơn hôm qua';
        }

        $change = (($today - $yesterday) / $yesterday) * 100;
        
        if ($change > 0) {
            return 'Tăng ' . number_format(abs($change), 1) . '% so với hôm qua';
        } elseif ($change < 0) {
            return 'Giảm ' . number_format(abs($change), 1) . '% so với hôm qua';
        }

        return 'Bằng hôm qua';
    }

    private function getRevenueChange($today, $yesterday): string
    {
        if ($yesterday == 0) {
            return $today > 0 ? 'Tăng 100%' : 'Không có doanh thu hôm qua';
        }

        $change = (($today - $yesterday) / $yesterday) * 100;
        
        if ($change > 0) {
            return 'Tăng ' . number_format(abs($change), 1) . '% so với hôm qua';
        } elseif ($change < 0) {
            return 'Giảm ' . number_format(abs($change), 1) . '% so với hôm qua';
        }

        return 'Bằng hôm qua';
    }

    private function getChangeIcon($current, $previous): string
    {
        if ($current > $previous) {
            return 'heroicon-m-arrow-trending-up';
        } elseif ($current < $previous) {
            return 'heroicon-m-arrow-trending-down';
        }

        return 'heroicon-m-minus';
    }

    private function getChangeColor($current, $previous): string
    {
        if ($current > $previous) {
            return 'success';
        } elseif ($current < $previous) {
            return 'danger';
        }

        return 'gray';
    }

    private function getOrdersChart(): array
    {
        // Lấy số đơn hàng 7 ngày gần nhất
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $count = Order::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getRevenueChart(): array
    {
        // Lấy doanh thu 7 ngày gần nhất
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $revenue = Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total');
            $data[] = (float) $revenue;
        }
        return $data;
    }
}
