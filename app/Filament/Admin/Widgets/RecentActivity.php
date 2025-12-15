<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class RecentActivity extends Widget
{
    protected static string $view = 'filament.admin.widgets.recent-activity';
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';

    // Auto refresh every 5 seconds
    protected static ?string $pollingInterval = '5s';

    public function getViewData(): array
    {
        return [
            'activities' => $this->getRecentActivities(),
        ];
    }

    private function getRecentActivities(): Collection
    {
        $activities = collect();

        // Đơn hàng mới (5 đơn gần nhất)
        $recentOrders = Order::latest()->limit(5)->get();
        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order',
                'icon' => 'heroicon-o-shopping-bag',
                'color' => $this->getOrderColor($order->status),
                'title' => "Đơn hàng {$order->order_number}",
                'description' => $this->getOrderDescription($order),
                'time' => $order->created_at,
                'url' => null, // route('filament.admin.resources.orders.view', $order),
            ]);
        }

        // Sản phẩm mới (3 sản phẩm gần nhất)
        $recentProducts = Product::latest()->limit(3)->get();
        foreach ($recentProducts as $product) {
            $activities->push([
                'type' => 'product',
                'icon' => 'heroicon-o-cube',
                'color' => 'info',
                'title' => "Sản phẩm mới: {$product->name}",
                'description' => "Giá: " . number_format($product->price) . " VNĐ - Tồn kho: {$product->stock}",
                'time' => $product->created_at,
                'url' => null, // route('filament.admin.resources.products.view', $product),
            ]);
        }

        // Khách hàng mới (3 khách hàng gần nhất)
        $recentCustomers = Customer::latest()->limit(3)->get();
        foreach ($recentCustomers as $customer) {
            $activities->push([
                'type' => 'customer',
                'icon' => 'heroicon-o-user-plus',
                'color' => 'success',
                'title' => "Khách hàng mới: {$customer->name}",
                'description' => $customer->email,
                'time' => $customer->created_at,
                'url' => null, // route('filament.admin.resources.customers.view', $customer),
            ]);
        }

        // Sản phẩm sắp hết hàng
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->where('stock', '>', 0)
            ->orderBy('stock')
            ->limit(3)
            ->get();

        foreach ($lowStockProducts as $product) {
            $activities->push([
                'type' => 'warning',
                'icon' => 'heroicon-o-exclamation-triangle',
                'color' => 'warning',
                'title' => "Sắp hết hàng: {$product->name}",
                'description' => "Chỉ còn {$product->stock} {$product->unit}",
                'time' => $product->updated_at,
                'url' => null, // route('filament.admin.resources.products.edit', $product),
            ]);
        }

        // Sắp xếp theo thời gian mới nhất
        return $activities->sortByDesc('time')->take(10);
    }

    private function getOrderColor(string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    private function getOrderDescription(Order $order): string
    {
        $statusText = match ($order->status) {
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định',
        };

        return "{$statusText} - " . number_format($order->total) . " VNĐ";
    }
}
