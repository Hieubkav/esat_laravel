<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Console\Command;

class CreateTestOrders extends Command
{
    protected $signature = 'create:test-orders {--count=5}';
    protected $description = 'Create test orders for dashboard testing';

    public function handle()
    {
        $count = $this->option('count');
        $this->info("Creating {$count} test orders...");

        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentMethods = ['cod', 'bank_transfer', 'online'];
        
        for ($i = 0; $i < $count; $i++) {
            $order = Order::create([
                'order_number' => 'TEST' . now()->format('YmdHis') . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'total' => rand(50000, 1500000),
                'status' => $statuses[array_rand($statuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'shipping_address' => 'Địa chỉ test ' . ($i + 1),
                'note' => 'Đơn hàng test cho dashboard',
                'created_at' => now()->subMinutes(rand(0, 1440)), // Random trong 24h qua
            ]);

            $this->info("Tạo đơn hàng: {$order->order_number} - " . number_format($order->total) . " VNĐ - {$order->status}");
        }

        $this->info("Hoàn thành! Đã tạo {$count} đơn hàng test.");
        $this->info("Tổng đơn hàng hiện tại: " . Order::count());
    }
}
