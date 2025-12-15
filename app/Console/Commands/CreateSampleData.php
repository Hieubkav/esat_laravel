<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CatProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateSampleData extends Command
{
    protected $signature = 'create:sample-data';
    protected $description = 'Create sample data for testing dashboard';

    public function handle()
    {
        $this->info('Creating sample data...');

        // Tạo categories nếu chưa có
        $categories = [
            'Bánh ngọt',
            'Bánh mặn', 
            'Đồ uống',
            'Nguyên liệu'
        ];

        foreach ($categories as $index => $name) {
            CatProduct::firstOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
                'status' => 'active',
                'order' => $index,
            ]);
        }

        // Tạo customers
        for ($i = 0; $i < 5; $i++) {
            Customer::create([
                'name' => 'Khách hàng ' . ($i + 1),
                'email' => 'customer' . ($i + 1) . '@example.com',
                'phone' => '0123456' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'address' => 'Địa chỉ khách hàng ' . ($i + 1),
                'status' => 'active',
                'order' => $i,
                'password' => bcrypt('password'),
            ]);
        }

        // Tạo products
        $categoryIds = CatProduct::pluck('id')->toArray();
        for ($i = 0; $i < 20; $i++) {
            Product::create([
                'name' => 'Sản phẩm ' . ($i + 1),
                'slug' => 'san-pham-' . ($i + 1),
                'description' => 'Mô tả sản phẩm ' . ($i + 1),
                'price' => rand(50000, 500000),
                'compare_price' => rand(60000, 600000),
                'brand' => 'Thương hiệu ' . chr(65 + ($i % 5)),
                'sku' => 'SKU' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'stock' => rand(10, 100),
                'unit' => ['cái', 'kg', 'hộp', 'gói'][rand(0, 3)],
                'is_hot' => rand(0, 1),
                'order' => $i,
                'status' => 'active',
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // Tạo orders với ngày khác nhau
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentMethods = ['cod', 'bank_transfer', 'online'];
        
        for ($i = 0; $i < 30; $i++) {
            Order::create([
                'order_number' => 'ORD' . now()->format('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'total' => rand(100000, 2000000),
                'status' => $statuses[array_rand($statuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'shipping_address' => 'Địa chỉ giao hàng ' . ($i + 1),
                'note' => 'Ghi chú đơn hàng ' . ($i + 1),
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
        }

        $this->info('Sample data created successfully!');
        $this->info('Created:');
        $this->info('- ' . CatProduct::count() . ' categories');
        $this->info('- ' . Customer::count() . ' customers');
        $this->info('- ' . Product::count() . ' products');
        $this->info('- ' . Order::count() . ' orders');
    }
}
