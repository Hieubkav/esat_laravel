<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Carbon\Carbon;

class ExecutiveKPI extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->subDays(30);
        $endDate = $this->filters['endDate'] ?? now();

        // KPI chính cho giám đốc
        $totalRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedOrders = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $avgOrderValue = $completedOrders > 0 ? $totalRevenue / $completedOrders : 0;

        // So sánh với kỳ trước
        $previousStartDate = Carbon::parse($startDate)->subDays(Carbon::parse($startDate)->diffInDays($endDate));
        $previousEndDate = Carbon::parse($startDate)->subDay();

        $previousRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('total');

        $previousOrders = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();

        // Tỷ lệ chuyển đổi
        $conversionRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;

        return [
            // KPI tài chính - Quan trọng nhất
            Stat::make('Tong Doanh Thu', number_format($totalRevenue, 0, ',', '.') . ' VND')
                ->description($this->getChangeDescription($totalRevenue, $previousRevenue, 'VND'))
                ->descriptionIcon($this->getChangeIcon($totalRevenue, $previousRevenue))
                ->color($this->getChangeColor($totalRevenue, $previousRevenue))
                ->chart($this->getRevenueChart())
                ->extraAttributes(['class' => 'executive-kpi-primary']),

            // Đơn hàng
            Stat::make('Tong Don Hang', number_format($totalOrders))
                ->description($this->getChangeDescription($totalOrders, $previousOrders, 'don'))
                ->descriptionIcon($this->getChangeIcon($totalOrders, $previousOrders))
                ->color($this->getChangeColor($totalOrders, $previousOrders))
                ->chart($this->getOrdersChart()),

            // Giá trị đơn hàng trung bình
            Stat::make('Gia Tri TB/Don', number_format($avgOrderValue, 0, ',', '.') . ' VND')
                ->description('Tu ' . number_format($completedOrders) . ' don hoan thanh')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),

            // Tỷ lệ chuyển đổi
            Stat::make('Ty Le Hoan Thanh', number_format($conversionRate, 1) . '%')
                ->description($completedOrders . '/' . $totalOrders . ' don hang')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($conversionRate >= 70 ? 'success' : ($conversionRate >= 50 ? 'warning' : 'danger')),
        ];
    }

    private function getChangeDescription($current, $previous, $unit = ''): string
    {
        if ($previous == 0) {
            return $current > 0 ? "Tăng 100% so với kỳ trước" : 'Không có dữ liệu kỳ trước';
        }

        $change = (($current - $previous) / $previous) * 100;
        $changeText = number_format(abs($change), 1) . '%';

        if ($change > 0) {
            return "↗️ Tăng {$changeText} so với kỳ trước";
        } elseif ($change < 0) {
            return "↘️ Giảm {$changeText} so với kỳ trước";
        }

        return "➡️ Không đổi so với kỳ trước";
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

    private function getRevenueChart(): array
    {
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

    private function getOrdersChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $count = Order::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }
}
