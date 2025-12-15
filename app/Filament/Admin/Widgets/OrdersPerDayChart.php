<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Carbon\Carbon;

class OrdersPerDayChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Đơn hàng theo ngày';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 2,
        'lg' => 2,
        'xl' => 3,
        '2xl' => 3,
    ];

    // Auto refresh every 30 seconds
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->subDays(30);
        $endDate = $this->filters['endDate'] ?? now();

        // Tạo mảng ngày từ startDate đến endDate
        $dates = [];
        $orderCounts = [];
        $labels = [];

        $currentDate = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        while ($currentDate->lte($endDateCarbon)) {
            $dateString = $currentDate->format('Y-m-d');
            $dates[] = $dateString;
            $labels[] = $currentDate->format('d/m');

            // Lấy số lượng đơn hàng cho ngày này
            $orderCount = Order::whereDate('created_at', $dateString)->count();
            $orderCounts[] = $orderCount;

            $currentDate->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Số đơn hàng',
                    'data' => $orderCounts,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Ngày'
                    ]
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Số đơn hàng'
                    ],
                    'beginAtZero' => true,
                ]
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }
}
