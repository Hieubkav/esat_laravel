<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class OrderStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Trạng thái đơn hàng';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '5s';
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 2,
        'xl' => 2,
        '2xl' => 3,
    ];

    public ?array $filters = [];

    protected $listeners = ['filtersUpdated' => '$refresh'];

    protected function getData(): array
    {
        $period = $this->filters['period'] ?? 'this_month';
        $dateRange = $this->getDateRange($period);

        $statusCounts = Order::whereBetween('created_at', $dateRange)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];

        $labels = [];
        $data = [];
        $colors = [];

        $statusColors = [
            'pending' => '#f59e0b',      // amber-500
            'processing' => '#3b82f6',   // blue-500
            'completed' => '#10b981',    // emerald-500
            'cancelled' => '#ef4444',    // red-500
        ];

        foreach ($statusLabels as $status => $label) {
            if (isset($statusCounts[$status]) && $statusCounts[$status] > 0) {
                $labels[] = $label;
                $data[] = $statusCounts[$status];
                $colors[] = $statusColors[$status];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng đơn hàng',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ": " + context.parsed + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '60%',
            'elements' => [
                'arc' => [
                    'borderWidth' => 2,
                ],
            ],
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

    public function getDescription(): ?string
    {
        $period = $this->filters['period'] ?? 'this_month';
        $dateRange = $this->getDateRange($period);

        $total = Order::whereBetween('created_at', $dateRange)->count();

        $periodLabels = [
            'today' => 'hôm nay',
            'yesterday' => 'hôm qua',
            'this_week' => 'tuần này',
            'this_month' => 'tháng này',
            'this_year' => 'năm nay',
            'last_year' => 'năm ngoái',
        ];

        $periodLabel = $periodLabels[$period] ?? 'khoảng thời gian được chọn';

        return "Tổng {$total} đơn hàng trong {$periodLabel}";
    }
}
