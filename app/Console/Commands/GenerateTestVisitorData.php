<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitor;
use App\Models\PostView;
use App\Models\ProductView;
use App\Models\Post;
use App\Models\Product;
use Carbon\Carbon;

class GenerateTestVisitorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitor:generate-test-data {--count=50 : Number of test records to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test visitor tracking data for demonstration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        
        $this->info("🚀 Bắt đầu tạo {$count} bản ghi dữ liệu test...");
        
        // Tạo dữ liệu visitor
        $this->generateVisitorData($count);
        
        // Tạo dữ liệu post views
        $this->generatePostViewData($count / 2);
        
        // Tạo dữ liệu product views
        $this->generateProductViewData($count / 2);
        
        $this->info("✅ Hoàn thành! Đã tạo dữ liệu test thành công.");
        $this->info("📊 Bạn có thể xem kết quả tại /admin/dashboard hoặc /admin/visitor-analytics");
        
        return 0;
    }

    private function generateVisitorData(int $count): void
    {
        $this->info("📈 Tạo dữ liệu lượt truy cập website...");
        
        $ips = [
            '192.168.1.1', '192.168.1.2', '192.168.1.3',
            '10.0.0.1', '10.0.0.2', '10.0.0.3',
            '172.16.0.1', '172.16.0.2', '172.16.0.3',
            '203.113.14.1', '203.113.14.2'
        ];

        $urls = [
            'http://localhost/',
            'http://localhost/posts',
            'http://localhost/products',
            'http://localhost/about',
            'http://localhost/contact',
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15',
            'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0 Firefox/88.0',
        ];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            Visitor::create([
                'ip_address' => $ips[array_rand($ips)],
                'user_agent' => $userAgents[array_rand($userAgents)],
                'session_id' => 'test_session_' . rand(1000, 9999),
                'url' => $urls[array_rand($urls)],
                'referer' => rand(0, 1) ? 'https://google.com' : null,
                'visited_at' => Carbon::now()->subMinutes(rand(0, 1440)), // Trong 24h qua
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
    }

    private function generatePostViewData(int $count): void
    {
        $this->info("📝 Tạo dữ liệu lượt xem bài viết...");
        
        $posts = Post::limit(10)->get();
        if ($posts->isEmpty()) {
            $this->warn("⚠️  Không tìm thấy bài viết nào. Bỏ qua tạo dữ liệu post views.");
            return;
        }

        $ips = [
            '192.168.1.1', '192.168.1.2', '192.168.1.3',
            '10.0.0.1', '10.0.0.2', '10.0.0.3',
            '172.16.0.1', '172.16.0.2'
        ];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $post = $posts->random();
            
            PostView::create([
                'post_id' => $post->id,
                'ip_address' => $ips[array_rand($ips)],
                'session_id' => 'test_session_' . rand(1000, 9999),
                'viewed_at' => Carbon::now()->subMinutes(rand(0, 1440)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
    }

    private function generateProductViewData(int $count): void
    {
        $this->info("🛍️  Tạo dữ liệu lượt xem sản phẩm...");
        
        $products = Product::limit(10)->get();
        if ($products->isEmpty()) {
            $this->warn("⚠️  Không tìm thấy sản phẩm nào. Bỏ qua tạo dữ liệu product views.");
            return;
        }

        $ips = [
            '192.168.1.1', '192.168.1.2', '192.168.1.3',
            '10.0.0.1', '10.0.0.2', '10.0.0.3',
            '172.16.0.1', '172.16.0.2'
        ];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $product = $products->random();
            
            ProductView::create([
                'product_id' => $product->id,
                'ip_address' => $ips[array_rand($ips)],
                'session_id' => 'test_session_' . rand(1000, 9999),
                'viewed_at' => Carbon::now()->subMinutes(rand(0, 1440)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
    }
}
