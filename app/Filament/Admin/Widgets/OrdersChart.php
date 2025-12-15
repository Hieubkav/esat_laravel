<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class OrdersChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Trạng thái đơn hàng';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 1,
        'xl' => 2,
        '2xl' => 2,
    ];

    // Auto refresh every 30 seconds
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query = Order::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $statusCounts = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];

        $colors = [
            'pending' => 'rgba(255, 193, 7, 0.8)',
            'processing' => 'rgba(13, 202, 240, 0.8)',
            'completed' => 'rgba(25, 135, 84, 0.8)',
            'cancelled' => 'rgba(220, 53, 69, 0.8)',
        ];

        $labels = [];
        $data = [];
        $backgroundColor = [];
        $borderColor = [];

        foreach ($statusLabels as $status => $label) {
            if (isset($statusCounts[$status]) && $statusCounts[$status] > 0) {
                $labels[] = $label;
                $data[] = $statusCounts[$status];
                $backgroundColor[] = $colors[$status];
                $borderColor[] = str_replace('0.8', '1', $colors[$status]);
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng đơn hàng',
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ": " + value + " đơn (" + percentage + "%)";
                        }'
                    ]
                ],
            ],
        ];
    }
}
