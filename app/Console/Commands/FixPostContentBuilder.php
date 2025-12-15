<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;

class FixPostContentBuilder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:fix-content-builder {slug?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix post content_builder by converting existing content to builder format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        if ($slug) {
            $post = Post::where('slug', $slug)->first();
            if (!$post) {
                $this->error("Post with slug '{$slug}' not found.");
                return 1;
            }
            $posts = collect([$post]);
        } else {
            $posts = Post::whereNull('content_builder')
                ->orWhere('content_builder', '[]')
                ->orWhere('content_builder', 'null')
                ->get();
        }

        $this->info("Found {$posts->count()} posts to fix.");

        foreach ($posts as $post) {
            $this->info("Fixing post: {$post->title}");

            // Tạo content_builder từ content hiện có
            $contentBuilder = [];

            if (!empty($post->content)) {
                // Tạo một paragraph block từ content hiện có
                $contentBuilder[] = [
                    'type' => 'paragraph',
                    'data' => [
                        'content' => $post->content
                    ]
                ];
            }

            // Cập nhật post
            $post->update([
                'content_builder' => $contentBuilder
            ]);

            $this->line("✓ Fixed: {$post->title}");
        }

        $this->info("Completed fixing {$posts->count()} posts.");
        return 0;
    }
}
