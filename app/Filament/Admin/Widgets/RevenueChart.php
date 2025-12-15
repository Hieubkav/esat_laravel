<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Biểu đồ doanh thu';
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '5s';
    protected int | string | array $columnSpan = 'full';

    public ?array $filters = [];

    protected $listeners = ['filtersUpdated' => '$refresh'];

    protected function getData(): array
    {
        $period = $this->filters['period'] ?? 'this_month';

        return match ($period) {
            'today', 'yesterday' => $this->getHourlyData($period),
            'this_week' => $this->getDailyDataForWeek(),
            'this_month' => $this->getDailyDataForMonth(),
            'this_year' => $this->getMonthlyDataForYear(),
            'last_year' => $this->getMonthlyDataForLastYear(),
            default => $this->getDailyDataForMonth(),
        };
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
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) {
                            return new Intl.NumberFormat("vi-VN", {
                                style: "currency",
                                currency: "VND",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.dataset.label + ": " + new Intl.NumberFormat("vi-VN", {
                                style: "currency",
                                currency: "VND",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(context.parsed.y);
                        }',
                    ],
                ],
            ],
            'elements' => [
                'line' => [
                    'tension' => 0.4,
                ],
                'point' => [
                    'radius' => 4,
                    'hoverRadius' => 6,
                ],
            ],
        ];
    }

    private function getHourlyData(string $period): array
    {
        $date = $period === 'today' ? Carbon::today() : Carbon::yesterday();

        $data = Order::whereDate('created_at', $date)
            ->whereIn('status', ['completed', 'processing'])
            ->selectRaw('HOUR(created_at) as hour, SUM(total) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Tạo array 24 giờ với giá trị 0
        $hourlyData = array_fill(0, 24, 0);
        $labels = [];

        for ($i = 0; $i < 24; $i++) {
            $labels[] = sprintf('%02d:00', $i);
        }

        // Điền dữ liệu thực tế
        foreach ($data as $item) {
            $hourlyData[$item->hour] = $item->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu (₫)',
                    'data' => $hourlyData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getDailyDataForWeek(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $data = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['completed', 'processing'])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Tạo array 7 ngày với giá trị 0
        $weeklyData = [];
        $labels = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateKey = $date->format('Y-m-d');
            $weeklyData[] = isset($data[$dateKey]) ? $data[$dateKey]->total : 0;
            $labels[] = $date->format('d/m');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu (₫)',
                    'data' => $weeklyData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getDailyDataForMonth(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $data = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['completed', 'processing'])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Tạo array cho tất cả ngày trong tháng
        $monthlyData = [];
        $labels = [];
        $daysInMonth = $endOfMonth->day;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = $startOfMonth->copy()->addDays($i - 1);
            $dateKey = $date->format('Y-m-d');
            $monthlyData[] = isset($data[$dateKey]) ? $data[$dateKey]->total : 0;
            $labels[] = $date->format('d/m');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu (₫)',
                    'data' => $monthlyData,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getMonthlyDataForYear(): array
    {
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        $data = Order::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->whereIn('status', ['completed', 'processing'])
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Tạo array 12 tháng với giá trị 0
        $yearlyData = [];
        $labels = [];

        for ($i = 1; $i <= 12; $i++) {
            $yearlyData[] = isset($data[$i]) ? $data[$i]->total : 0;
            $labels[] = sprintf('%02d/%s', $i, Carbon::now()->year);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu (₫)',
                    'data' => $yearlyData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getMonthlyDataForLastYear(): array
    {
        $startOfLastYear = Carbon::now()->subYear()->startOfYear();
        $endOfLastYear = Carbon::now()->subYear()->endOfYear();

        $data = Order::whereBetween('created_at', [$startOfLastYear, $endOfLastYear])
            ->whereIn('status', ['completed', 'processing'])
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Tạo array 12 tháng với giá trị 0
        $lastYearData = [];
        $labels = [];

        for ($i = 1; $i <= 12; $i++) {
            $lastYearData[] = isset($data[$i]) ? $data[$i]->total : 0;
            $labels[] = sprintf('%02d/%s', $i, Carbon::now()->subYear()->year);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu năm ngoái (₫)',
                    'data' => $lastYearData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getDescription(): ?string
    {
        $period = $this->filters['period'] ?? 'this_month';

        $descriptions = [
            'today' => 'Doanh thu theo giờ trong ngày hôm nay',
            'yesterday' => 'Doanh thu theo giờ trong ngày hôm qua',
            'this_week' => 'Doanh thu theo ngày trong tuần này',
            'this_month' => 'Doanh thu theo ngày trong tháng này',
            'this_year' => 'Doanh thu theo tháng trong năm nay',
            'last_year' => 'Doanh thu theo tháng trong năm ngoái',
        ];

        return $descriptions[$period] ?? 'Biểu đồ doanh thu theo thời gian';
    }
}
