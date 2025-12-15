<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;

class CleanupTiptapMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tiptap:cleanup-media {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup unused TiptapEditor media files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = Storage::disk('public');
        $mediaPath = 'posts/tiptap-media';
        $isDryRun = $this->option('dry-run');

        if (!$disk->exists($mediaPath)) {
            $this->info("Media directory does not exist: {$mediaPath}");
            return;
        }

        // Lấy tất cả file trong thư mục
        $files = $disk->files($mediaPath);
        $this->info("Found " . count($files) . " files in {$mediaPath}");

        // Lấy tất cả nội dung bài viết
        $posts = Post::all();
        $usedFiles = [];

        $this->info("Scanning " . count($posts) . " posts for media usage...");

        foreach ($posts as $post) {
            // Tìm tất cả đường dẫn ảnh trong content_builder
            if ($post->content_builder) {
                $content = json_encode($post->content_builder);
                preg_match_all('/posts\/tiptap-media\/[^"\']+/', $content, $matches);
                if (!empty($matches[0])) {
                    $usedFiles = array_merge($usedFiles, $matches[0]);
                }
            }
        }

        $usedFiles = array_unique($usedFiles);
        $this->info("Found " . count($usedFiles) . " files in use");

        // Tìm file không sử dụng
        $unusedFiles = [];
        foreach ($files as $file) {
            if (!in_array($file, $usedFiles)) {
                $unusedFiles[] = $file;
            }
        }

        if (empty($unusedFiles)) {
            $this->info("No unused files found!");
            return;
        }

        $this->warn("Found " . count($unusedFiles) . " unused files:");

        $totalSize = 0;
        foreach ($unusedFiles as $file) {
            $size = $disk->size($file);
            $totalSize += $size;
            $sizeFormatted = $this->formatBytes($size);

            if ($isDryRun) {
                $this->line("Would delete: {$file} ({$sizeFormatted})");
            } else {
                $disk->delete($file);
                $this->info("Deleted: {$file} ({$sizeFormatted})");
            }
        }

        $totalSizeFormatted = $this->formatBytes($totalSize);

        if ($isDryRun) {
            $this->warn("DRY RUN: Would delete " . count($unusedFiles) . " files ({$totalSizeFormatted})");
            $this->info("Run without --dry-run to actually delete the files");
        } else {
            $this->info("Cleanup completed! Deleted " . count($unusedFiles) . " files ({$totalSizeFormatted})");
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
