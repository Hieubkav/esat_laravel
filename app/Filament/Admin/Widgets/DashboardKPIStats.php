<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class DashboardKPIStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '5s';

    public ?array $filters = [];

    protected $listeners = ['filtersUpdated' => '$refresh'];

    protected function getStats(): array
    {
        $period = $this->filters['period'] ?? 'this_month';
        $dateRange = $this->getDateRange($period);

        // Tổng doanh thu
        $totalRevenue = Order::whereBetween('created_at', $dateRange)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('total');

        // Tổng đơn hàng
        $totalOrders = Order::whereBetween('created_at', $dateRange)->count();

        // Tổng khách hàng mới
        $totalCustomers = Customer::whereBetween('created_at', $dateRange)->count();

        // Tỉ lệ hoàn thành đơn hàng
        $completedOrders = Order::whereBetween('created_at', $dateRange)
            ->where('status', 'completed')
            ->count();
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;

        // Tính toán so sánh với kỳ trước
        $previousDateRange = $this->getPreviousDateRange($period);

        $previousRevenue = Order::whereBetween('created_at', $previousDateRange)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('total');

        $previousOrders = Order::whereBetween('created_at', $previousDateRange)->count();

        $previousCustomers = Customer::whereBetween('created_at', $previousDateRange)->count();

        // Tính phần trăm thay đổi
        $revenueChange = $this->calculatePercentageChange($totalRevenue, $previousRevenue);
        $ordersChange = $this->calculatePercentageChange($totalOrders, $previousOrders);
        $customersChange = $this->calculatePercentageChange($totalCustomers, $previousCustomers);

        return [
            Stat::make('Tổng doanh thu', number_format($totalRevenue, 0, ',', '.') . ' ₫')
                ->description($this->getChangeDescription($revenueChange, 'doanh thu'))
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($this->getRevenueChart($period)),

            Stat::make('Tổng đơn hàng', number_format($totalOrders))
                ->description($this->getChangeDescription($ordersChange, 'đơn hàng'))
                ->descriptionIcon($ordersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersChange >= 0 ? 'success' : 'danger')
                ->chart($this->getOrdersChart($period)),

            Stat::make('Khách hàng mới', number_format($totalCustomers))
                ->description($this->getChangeDescription($customersChange, 'khách hàng'))
                ->descriptionIcon($customersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($customersChange >= 0 ? 'success' : 'danger')
                ->chart($this->getCustomersChart($period)),

            Stat::make('Tỉ lệ hoàn thành', $completionRate . '%')
                ->description('Đơn hàng hoàn thành / Tổng đơn hàng')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($completionRate >= 80 ? 'success' : ($completionRate >= 60 ? 'warning' : 'danger')),
        ];
    }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            'today' => [Carbon::today(), Carbon::today()->endOfDay()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()->endOfDay()],
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'last_year' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    private function getPreviousDateRange(string $period): array
    {
        return match ($period) {
            'today' => [Carbon::yesterday(), Carbon::yesterday()->endOfDay()],
            'yesterday' => [Carbon::yesterday()->subDay(), Carbon::yesterday()->subDay()->endOfDay()],
            'this_week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'this_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'this_year' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            'last_year' => [Carbon::now()->subYears(2)->startOfYear(), Carbon::now()->subYears(2)->endOfYear()],
            default => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
        };
    }

    private function calculatePercentageChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getChangeDescription(float $change, string $type): string
    {
        $direction = $change >= 0 ? 'tăng' : 'giảm';
        return abs($change) . "% {$direction} {$type} so với kỳ trước";
    }

    private function getRevenueChart(string $period): array
    {
        $dateRange = $this->getDateRange($period);

        return Order::whereBetween('created_at', $dateRange)
            ->whereIn('status', ['completed', 'processing'])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total')
            ->toArray();
    }

    private function getOrdersChart(string $period): array
    {
        $dateRange = $this->getDateRange($period);

        return Order::whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function getCustomersChart(string $period): array
    {
        $dateRange = $this->getDateRange($period);

        return Customer::whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }
}
