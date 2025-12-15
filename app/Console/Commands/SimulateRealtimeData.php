<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Console\Command;

class SimulateRealtimeData extends Command
{
    protected $signature = 'simulate:realtime-data {--duration=60}';
    protected $description = 'Simulate realtime data by creating orders every few seconds';

    public function handle()
    {
        $duration = $this->option('duration');
        $this->info("Simulating realtime data for {$duration} seconds...");
        $this->info('Press Ctrl+C to stop');

        $startTime = time();
        $orderCount = 0;

        while ((time() - $startTime) < $duration) {
            // Tạo đơn hàng mới
            $order = Order::create([
                'order_number' => 'SIM' . now()->format('YmdHis') . rand(100, 999),
                'total' => rand(50000, 1000000),
                'status' => ['pending', 'processing', 'completed'][rand(0, 2)],
                'payment_method' => ['cod', 'bank_transfer', 'online'][rand(0, 2)],
                'shipping_address' => 'Địa chỉ mô phỏng ' . rand(1, 100),
                'note' => 'Đơn hàng mô phỏng realtime',
                'created_at' => now(),
            ]);

            $orderCount++;
            $this->info("Tạo đơn hàng #{$orderCount}: {$order->order_number} - " . number_format($order->total) . " VNĐ");

            // Đôi khi cập nhật stock của sản phẩm
            if (rand(1, 3) === 1) {
                $product = Product::inRandomOrder()->first();
                if ($product) {
                    $oldStock = $product->stock;
                    $product->stock = max(0, $product->stock - rand(1, 5));
                    $product->save();
                    $this->info("Cập nhật stock sản phẩm {$product->name}: {$oldStock} → {$product->stock}");
                }
            }

            // Đôi khi thay đổi trạng thái đơn hàng cũ
            if (rand(1, 4) === 1) {
                $oldOrder = Order::where('status', 'pending')->inRandomOrder()->first();
                if ($oldOrder) {
                    $oldStatus = $oldOrder->status;
                    $oldOrder->status = ['processing', 'completed'][rand(0, 1)];
                    $oldOrder->save();
                    $this->info("Cập nhật trạng thái đơn {$oldOrder->order_number}: {$oldStatus} → {$oldOrder->status}");
                }
            }

            // Chờ 3-8 giây trước khi tạo đơn tiếp theo
            $waitTime = rand(3, 8);
            $this->info("Chờ {$waitTime} giây...\n");
            sleep($waitTime);
        }

        $this->info("\nHoàn thành! Đã tạo {$orderCount} đơn hàng trong {$duration} giây.");
    }
}
