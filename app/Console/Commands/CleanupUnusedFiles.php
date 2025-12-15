<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\WebDesign;
use App\Models\Product;
use App\Models\Post;
use App\Models\Partner;
use App\Models\Employee;
use App\Models\Slider;

class CleanupUnusedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:cleanup-unused 
                            {--dry-run : Chỉ hiển thị file sẽ bị xóa mà không thực sự xóa}
                            {--directory= : Chỉ cleanup thư mục cụ thể (services, posts, products, etc.)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup các file ảnh không sử dụng trong storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $specificDirectory = $this->option('directory');

        $this->info('🧹 Bắt đầu cleanup file không sử dụng...');
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - Không file nào sẽ bị xóa thực sự');
        }

        $directories = $specificDirectory ? [$specificDirectory] : [
            'services',
            'posts',
            'products', 
            'partners',
            'employees',
            'sliders'
        ];

        $totalDeleted = 0;
        $totalSize = 0;

        foreach ($directories as $directory) {
            $result = $this->cleanupDirectory($directory, $dryRun);
            $totalDeleted += $result['count'];
            $totalSize += $result['size'];
        }

        $this->info("✅ Hoàn thành cleanup!");
        $this->info("📊 Tổng kết:");
        $this->info("   - File đã xóa: {$totalDeleted}");
        $this->info("   - Dung lượng tiết kiệm: " . $this->formatBytes($totalSize));

        return Command::SUCCESS;
    }

    /**
     * Cleanup một thư mục cụ thể
     */
    private function cleanupDirectory(string $directory, bool $dryRun): array
    {
        $this->info("🔍 Đang kiểm tra thư mục: {$directory}");

        $files = Storage::disk('public')->files($directory);
        $usedFiles = $this->getUsedFiles($directory);
        
        $unusedFiles = array_diff($files, $usedFiles);
        
        $deletedCount = 0;
        $deletedSize = 0;

        foreach ($unusedFiles as $file) {
            $size = Storage::disk('public')->size($file);
            $deletedSize += $size;
            
            if ($dryRun) {
                $this->line("   🗑️  Sẽ xóa: {$file} (" . $this->formatBytes($size) . ")");
            } else {
                Storage::disk('public')->delete($file);
                $this->line("   ✅ Đã xóa: {$file} (" . $this->formatBytes($size) . ")");
            }
            
            $deletedCount++;
        }

        if ($deletedCount === 0) {
            $this->info("   ✨ Thư mục {$directory} đã sạch!");
        } else {
            $this->info("   📁 {$directory}: {$deletedCount} file, " . $this->formatBytes($deletedSize));
        }

        return ['count' => $deletedCount, 'size' => $deletedSize];
    }

    /**
     * Lấy danh sách file đang được sử dụng
     */
    private function getUsedFiles(string $directory): array
    {
        $usedFiles = [];

        switch ($directory) {
            case 'services':
                $usedFiles = $this->getWebDesignServiceFiles();
                break;
            case 'posts':
                $usedFiles = $this->getPostFiles();
                break;
            case 'products':
                $usedFiles = $this->getProductFiles();
                break;
            case 'partners':
                $usedFiles = $this->getPartnerFiles();
                break;
            case 'employees':
                $usedFiles = $this->getEmployeeFiles();
                break;
            case 'sliders':
                $usedFiles = $this->getSliderFiles();
                break;
        }

        return array_filter($usedFiles);
    }

    /**
     * Lấy file services từ WebDesign
     */
    private function getWebDesignServiceFiles(): array
    {
        $files = [];
        
        $webDesigns = WebDesign::where('component_key', 'about-us')->get();
        
        foreach ($webDesigns as $webDesign) {
            if (isset($webDesign->content['services'])) {
                foreach ($webDesign->content['services'] as $service) {
                    if (isset($service['image']) && str_starts_with($service['image'], '/storage/')) {
                        $files[] = str_replace('/storage/', '', $service['image']);
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Lấy file posts
     */
    private function getPostFiles(): array
    {
        $files = [];
        
        // Thumbnail và OG images
        $posts = Post::whereNotNull('thumbnail')->orWhereNotNull('og_image_link')->get();
        foreach ($posts as $post) {
            if ($post->thumbnail) {
                $files[] = $post->thumbnail;
            }
            if ($post->og_image_link) {
                $files[] = $post->og_image_link;
            }
        }

        // Post images
        $postImages = DB::table('post_images')->whereNotNull('image_link')->pluck('image_link');
        $files = array_merge($files, $postImages->toArray());

        return $files;
    }

    /**
     * Lấy file products
     */
    private function getProductFiles(): array
    {
        $files = [];
        
        $productImages = DB::table('product_images')->whereNotNull('image_link')->pluck('image_link');
        $files = array_merge($files, $productImages->toArray());

        return $files;
    }

    /**
     * Lấy file partners
     */
    private function getPartnerFiles(): array
    {
        return Partner::whereNotNull('logo_link')->pluck('logo_link')->toArray();
    }

    /**
     * Lấy file employees
     */
    private function getEmployeeFiles(): array
    {
        $files = [];
        
        $employeeImages = DB::table('employee_images')->whereNotNull('image_link')->pluck('image_link');
        $files = array_merge($files, $employeeImages->toArray());

        return $files;
    }

    /**
     * Lấy file sliders
     */
    private function getSliderFiles(): array
    {
        return Slider::whereNotNull('image_link')->pluck('image_link')->toArray();
    }

    /**
     * Format bytes thành đơn vị dễ đọc
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
