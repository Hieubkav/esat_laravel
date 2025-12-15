<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use App\Models\Order;
use Filament\Widgets\Widget;

class AlertsOverview extends Widget
{
    protected static string $view = 'filament.admin.widgets.alerts-overview';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
        '2xl' => 3,
    ];
    protected static ?string $pollingInterval = '5s';

    public function getViewData(): array
    {
        return [
            'alerts' => $this->getAlerts(),
        ];
    }

    private function getAlerts(): array
    {
        $alerts = [];

        // Đơn hàng cần xử lý
        $pendingOrders = Order::where('status', 'pending')->count();
        if ($pendingOrders > 0) {
            $alerts[] = [
                'type' => 'urgent',
                'icon' => 'heroicon-o-clock',
                'title' => "🚨 {$pendingOrders} đơn hàng chờ xử lý",
                'description' => 'Cần xử lý ngay để đảm bảo dịch vụ khách hàng',
                'action' => 'Xem đơn hàng',
                'color' => 'danger'
            ];
        }

        // Sản phẩm hết hàng
        $outOfStock = Product::where('stock', 0)->where('status', 'active')->count();
        if ($outOfStock > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'heroicon-o-x-circle',
                'title' => "⚠️ {$outOfStock} sản phẩm hết hàng",
                'description' => 'Cần nhập hàng hoặc tạm ẩn sản phẩm',
                'action' => 'Xem sản phẩm',
                'color' => 'danger'
            ];
        }

        // Sản phẩm sắp hết hàng
        $lowStock = Product::where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->where('status', 'active')
            ->count();
        if ($lowStock > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'heroicon-o-exclamation-triangle',
                'title' => "⚠️ {$lowStock} sản phẩm sắp hết",
                'description' => 'Tồn kho dưới 10 sản phẩm, cần nhập thêm',
                'action' => 'Xem chi tiết',
                'color' => 'warning'
            ];
        }

        // Đơn hàng hôm nay
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        if ($todayOrders > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'heroicon-o-shopping-bag',
                'title' => "📈 {$todayOrders} đơn hàng hôm nay",
                'description' => 'Doanh thu: ' . number_format($todayRevenue, 0, ',', '.') . ' VNĐ',
                'action' => 'Xem báo cáo',
                'color' => 'success'
            ];
        }

        // Nếu không có alert nào
        if (empty($alerts)) {
            $alerts[] = [
                'type' => 'success',
                'icon' => 'heroicon-o-check-circle',
                'title' => '✅ Mọi thứ đều ổn',
                'description' => 'Không có vấn đề nào cần chú ý',
                'action' => '',
                'color' => 'success'
            ];
        }

        return $alerts;
    }
}
