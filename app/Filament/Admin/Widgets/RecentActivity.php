<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Post;
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
                'url' => null,
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
                'description' => "Giá: " . number_format($product->price) . " VNĐ",
                'time' => $product->created_at,
                'url' => null,
            ]);
        }

        // Bài viết mới (3 bài viết gần nhất)
        $recentPosts = Post::latest()->limit(3)->get();
        foreach ($recentPosts as $post) {
            $activities->push([
                'type' => 'post',
                'icon' => 'heroicon-o-document-text',
                'color' => 'success',
                'title' => "Bài viết mới: {$post->title}",
                'description' => $post->excerpt ?? 'Không có mô tả',
                'time' => $post->created_at,
                'url' => null,
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
