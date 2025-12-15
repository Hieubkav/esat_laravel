<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;

class QuickDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 5 khách hàng mẫu
        $customers = [];
        for ($i = 1; $i <= 5; $i++) {
            $customers[] = Customer::create([
                'name' => "Khách hàng {$i}",
                'email' => "customer{$i}@test.com",
                'phone' => "090000000{$i}",
                'address' => "Địa chỉ khách hàng {$i}",
                'status' => 'active',
                'order' => $i,
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }

        // Tạo 5 sản phẩm mẫu
        $products = [];
        for ($i = 1; $i <= 5; $i++) {
            $products[] = Product::create([
                'name' => "Bánh {$i}",
                'description' => "Mô tả bánh {$i}",
                'slug' => "banh-{$i}",
                'price' => rand(50000, 500000),
                'compare_price' => rand(600000, 700000),
                'stock' => rand(10, 100),
                'status' => 'active',
                'order' => $i,
                'created_at' => Carbon::now()->subDays(rand(0, 60)),
            ]);
        }

        // Tạo đơn hàng mẫu
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        
        // Đơn hàng hôm nay
        for ($i = 0; $i < 3; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::today()->addHours(rand(8, 18)));
        }

        // Đơn hàng hôm qua
        for ($i = 0; $i < 5; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::yesterday()->addHours(rand(8, 18)));
        }

        // Đơn hàng tuần này
        for ($i = 0; $i < 15; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::now()->startOfWeek()->addDays(rand(0, 6))->addHours(rand(8, 18)));
        }

        // Đơn hàng tháng này
        for ($i = 0; $i < 30; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::now()->startOfMonth()->addDays(rand(0, Carbon::now()->day - 1))->addHours(rand(8, 18)));
        }
    }

    private function createOrder($customers, $products, $statuses, $createdAt)
    {
        $customer = $customers[array_rand($customers)];
        $status = $statuses[array_rand($statuses)];
        
        // Tăng tỷ lệ completed orders
        if (rand(1, 100) <= 60) {
            $status = 'completed';
        } elseif (rand(1, 100) <= 80) {
            $status = 'processing';
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'order_number' => 'ORD' . time() . rand(100, 999),
            'total' => 0, // Sẽ được tính lại sau
            'status' => $status,
            'payment_method' => ['cod', 'bank_transfer', 'online'][array_rand(['cod', 'bank_transfer', 'online'])],
            'shipping_address' => $customer->address,
            'note' => "Ghi chú đơn hàng test",
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        // Tạo order items
        $numItems = rand(1, 3);
        $total = 0;

        for ($j = 0; $j < $numItems; $j++) {
            $product = $products[array_rand($products)];
            $quantity = rand(1, 2);
            $price = $product->price;
            $subtotal = $quantity * $price;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $total += $subtotal;
        }

        // Cập nhật tổng tiền đơn hàng
        $order->update(['total' => $total]);
    }
}
