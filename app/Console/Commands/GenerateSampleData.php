<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\CatProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSampleData extends Command
{
    protected $signature = 'dashboard:generate-sample-data {--count=50}';
    protected $description = 'Generate sample data for dashboard testing';

    public function handle()
    {
        $count = $this->option('count');

        $this->info("Generating {$count} sample records...");

        // Create sample categories
        $categories = [];
        $categoryNames = ['Bánh ngọt', 'Bánh mặn', 'Đồ uống', 'Nguyên liệu', 'Bánh sinh nhật'];

        foreach ($categoryNames as $index => $name) {
            $categories[] = CatProduct::firstOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
                'status' => 'active',
                'order' => $index,
            ]);
        }

        // Create sample customers
        $customers = [];
        for ($i = 0; $i < min($count, 20); $i++) {
            $customers[] = Customer::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->email(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'status' => 'active',
                'order' => 0,
                'password' => bcrypt('password'),
            ]);
        }

        // Create sample products
        $products = [];
        for ($i = 0; $i < $count; $i++) {
            $category = fake()->randomElement($categories);
            $name = fake()->words(3, true);

            $products[] = Product::create([
                'name' => $name,
                'slug' => Str::slug($name) . '-' . $i,
                'description' => fake()->paragraph(),
                'price' => fake()->numberBetween(10000, 500000),
                'compare_price' => fake()->optional(0.3)->numberBetween(15000, 600000),
                'brand' => fake()->optional()->company(),
                'sku' => 'SKU-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'stock' => fake()->numberBetween(0, 100),
                'unit' => fake()->randomElement(['cái', 'kg', 'hộp', 'gói']),
                'is_hot' => fake()->boolean(20),
                'order' => $i,
                'status' => 'active',
                'category_id' => $category->id,
                'seo_title' => $name,
                'seo_description' => fake()->sentence(),
                'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            ]);
        }

        // Create sample orders
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentMethods = ['cod', 'bank_transfer', 'online'];

        for ($i = 0; $i < $count; $i++) {
            $customer = fake()->optional(0.7)->randomElement($customers);
            $status = fake()->randomElement($statuses);

            $order = Order::create([
                'customer_id' => $customer?->id,
                'order_number' => Order::generateOrderNumber(),
                'total' => 0, // Will be calculated later
                'status' => $status,
                'payment_method' => fake()->randomElement($paymentMethods),
                'shipping_address' => fake()->address(),
                'note' => fake()->optional()->sentence(),
                'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            ]);

            // Create order items
            $itemCount = fake()->numberBetween(1, 5);
            $total = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = fake()->randomElement($products);
                $quantity = fake()->numberBetween(1, 3);
                $price = $product->price;
                $subtotal = $quantity * $price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update(['total' => $total]);
        }

        $this->info("Sample data generated successfully!");
        $this->info("Created:");
        $this->info("- " . count($categories) . " categories");
        $this->info("- " . count($customers) . " customers");
        $this->info("- " . count($products) . " products");
        $this->info("- {$count} orders");
    }
}
