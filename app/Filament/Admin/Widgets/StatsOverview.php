<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    // Auto refresh every 5 seconds
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        // Total Products
        $totalProducts = Product::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $previousProducts = Product::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '<', $startDate))
            ->count();

        // Total Orders
        $totalOrders = Order::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $previousOrders = Order::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '<', $startDate))
            ->count();

        // Total Revenue
        $totalRevenue = Order::query()
            ->where('status', 'completed')
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->sum('total');

        $previousRevenue = Order::query()
            ->where('status', 'completed')
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '<', $startDate))
            ->sum('total');

        // Total Customers
        $totalCustomers = Customer::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $previousCustomers = Customer::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '<', $startDate))
            ->count();

        return [
            Stat::make('Tổng sản phẩm', $totalProducts)
                ->description($this->getChangeDescription($totalProducts, $previousProducts))
                ->descriptionIcon($this->getChangeIcon($totalProducts, $previousProducts))
                ->color($this->getChangeColor($totalProducts, $previousProducts))
                ->chart($this->getProductChart()),

            Stat::make('Tổng đơn hàng', $totalOrders)
                ->description($this->getChangeDescription($totalOrders, $previousOrders))
                ->descriptionIcon($this->getChangeIcon($totalOrders, $previousOrders))
                ->color($this->getChangeColor($totalOrders, $previousOrders))
                ->chart($this->getOrderChart()),

            Stat::make('Doanh thu', number_format($totalRevenue, 0, ',', '.') . ' VNĐ')
                ->description($this->getChangeDescription($totalRevenue, $previousRevenue))
                ->descriptionIcon($this->getChangeIcon($totalRevenue, $previousRevenue))
                ->color($this->getChangeColor($totalRevenue, $previousRevenue))
                ->chart($this->getRevenueChart()),

            Stat::make('Khách hàng', $totalCustomers)
                ->description($this->getChangeDescription($totalCustomers, $previousCustomers))
                ->descriptionIcon($this->getChangeIcon($totalCustomers, $previousCustomers))
                ->color($this->getChangeColor($totalCustomers, $previousCustomers))
                ->chart($this->getCustomerChart()),
        ];
    }

    private function getChangeDescription($current, $previous): string
    {
        if ($previous == 0) {
            return $current > 0 ? 'Tăng 100%' : 'Không thay đổi';
        }

        $change = (($current - $previous) / $previous) * 100;
        $changeText = abs($change) > 0 ? number_format(abs($change), 1) . '%' : 'Không đổi';

        if ($change > 0) {
            return "Tăng {$changeText}";
        } elseif ($change < 0) {
            return "Giảm {$changeText}";
        }

        return 'Không thay đổi';
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

    private function getProductChart(): array
    {
        return Product::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function getOrderChart(): array
    {
        return Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function getRevenueChart(): array
    {
        return Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total')
            ->toArray();
    }

    private function getCustomerChart(): array
    {
        return Customer::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }
}
