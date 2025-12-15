<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Visitor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class VisitorStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        // Lấy dữ liệu hôm nay
        $todayVisits = Visitor::getTodayVisits();
        $todayUniqueVisitors = Visitor::getTodayUniqueVisitors();
        
        // Lấy dữ liệu tổng
        $totalVisits = Visitor::getTotalVisits();
        $totalUniqueVisitors = Visitor::getTotalUniqueVisitors();

        // Tính toán thay đổi so với hôm qua
        $yesterdayVisits = Visitor::whereDate('visited_at', Carbon::yesterday())->count();
        $yesterdayUniqueVisitors = Visitor::whereDate('visited_at', Carbon::yesterday())
            ->distinct('ip_address')->count();

        $visitsChange = $yesterdayVisits > 0 
            ? round((($todayVisits - $yesterdayVisits) / $yesterdayVisits) * 100, 1)
            : 0;

        $uniqueVisitorsChange = $yesterdayUniqueVisitors > 0 
            ? round((($todayUniqueVisitors - $yesterdayUniqueVisitors) / $yesterdayUniqueVisitors) * 100, 1)
            : 0;

        return [
            Stat::make('Lượt truy cập hôm nay', number_format($todayVisits))
                ->description($this->getChangeDescription($visitsChange, 'so với hôm qua'))
                ->descriptionIcon($visitsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($visitsChange >= 0 ? 'success' : 'danger')
                ->chart($this->getTodayVisitsChart()),

            Stat::make('Người dùng khác nhau hôm nay', number_format($todayUniqueVisitors))
                ->description($this->getChangeDescription($uniqueVisitorsChange, 'so với hôm qua'))
                ->descriptionIcon($uniqueVisitorsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($uniqueVisitorsChange >= 0 ? 'success' : 'danger'),

            Stat::make('Tổng lượt truy cập', number_format($totalVisits))
                ->description('Từ khi bắt đầu tracking')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info'),

            Stat::make('Tổng người dùng khác nhau', number_format($totalUniqueVisitors))
                ->description('Từ khi bắt đầu tracking')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }

    protected function getChangeDescription(float $change, string $suffix): string
    {
        if ($change > 0) {
            return "+{$change}% {$suffix}";
        } elseif ($change < 0) {
            return "{$change}% {$suffix}";
        }
        return "Không thay đổi {$suffix}";
    }

    protected function getTodayVisitsChart(): array
    {
        // Lấy dữ liệu 24h qua theo từng giờ
        $data = [];
        for ($i = 23; $i >= 0; $i--) {
            $hour = Carbon::now()->subHours($i);
            $visits = Visitor::whereBetween('visited_at', [
                $hour->copy()->startOfHour(),
                $hour->copy()->endOfHour()
            ])->count();
            $data[] = $visits;
        }
        
        return $data;
    }


}
