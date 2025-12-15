<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;

class DashboardTestSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo khách hàng mẫu
        $customers = [];
        for ($i = 1; $i <= 20; $i++) {
            $customers[] = Customer::create([
                'name' => "Khách hàng {$i}",
                'email' => "customer{$i}@example.com",
                'phone' => "090000000{$i}",
                'address' => "Địa chỉ khách hàng {$i}",
                'status' => 'active',
                'created_at' => Carbon::now()->subDays(rand(0, 365)),
            ]);
        }

        // Tạo sản phẩm mẫu
        $products = [];
        for ($i = 1; $i <= 10; $i++) {
            $products[] = Product::create([
                'name' => "Sản phẩm {$i}",
                'description' => "Mô tả sản phẩm {$i}",
                'slug' => "san-pham-{$i}",
                'price' => rand(100000, 1000000),
                'compare_price' => rand(1100000, 1200000),
                'stock' => rand(10, 100),
                'status' => 'active',
                'order' => $i,
                'created_at' => Carbon::now()->subDays(rand(0, 180)),
            ]);
        }

        // Tạo đơn hàng mẫu cho các khoảng thời gian khác nhau
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        
        // Đơn hàng hôm nay
        for ($i = 0; $i < 5; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::today()->addHours(rand(8, 20)));
        }

        // Đơn hàng hôm qua
        for ($i = 0; $i < 8; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::yesterday()->addHours(rand(8, 20)));
        }

        // Đơn hàng tuần này
        for ($i = 0; $i < 25; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::now()->startOfWeek()->addDays(rand(0, 6))->addHours(rand(8, 20)));
        }

        // Đơn hàng tháng này
        for ($i = 0; $i < 80; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::now()->startOfMonth()->addDays(rand(0, Carbon::now()->day - 1))->addHours(rand(8, 20)));
        }

        // Đơn hàng năm nay
        for ($i = 0; $i < 200; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::now()->startOfYear()->addDays(rand(0, Carbon::now()->dayOfYear - 1))->addHours(rand(8, 20)));
        }

        // Đơn hàng năm ngoái
        for ($i = 0; $i < 150; $i++) {
            $this->createOrder($customers, $products, $statuses, Carbon::now()->subYear()->startOfYear()->addDays(rand(0, 364))->addHours(rand(8, 20)));
        }
    }

    private function createOrder($customers, $products, $statuses, $createdAt)
    {
        $customer = $customers[array_rand($customers)];
        $status = $statuses[array_rand($statuses)];
        
        // Tăng tỷ lệ completed orders
        if (rand(1, 100) <= 70) {
            $status = 'completed';
        } elseif (rand(1, 100) <= 85) {
            $status = 'processing';
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'order_number' => Order::generateOrderNumber(),
            'total' => 0, // Sẽ được tính lại sau
            'status' => $status,
            'payment_method' => ['cod', 'bank_transfer', 'online'][array_rand(['cod', 'bank_transfer', 'online'])],
            'shipping_address' => $customer->address,
            'note' => "Ghi chú đơn hàng",
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        // Tạo order items
        $numItems = rand(1, 4);
        $total = 0;

        for ($j = 0; $j < $numItems; $j++) {
            $product = $products[array_rand($products)];
            $quantity = rand(1, 3);
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
