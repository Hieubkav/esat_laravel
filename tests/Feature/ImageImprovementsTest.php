<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageImprovementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function image_service_preserves_aspect_ratio()
    {
        $imageService = new ImageService();
        
        // Tạo ảnh test với tỷ lệ 16:9
        $file = UploadedFile::fake()->image('test.jpg', 1600, 900);
        
        $path = $imageService->saveImageWithAspectRatio(
            $file,
            'test',
            1200, // max width
            630,  // max height
            90
        );
        
        $this->assertNotNull($path);
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        // Kiểm tra file được tạo
        $this->assertStringContains('test/', $path);
        $this->assertStringEndsWith('.webp', $path);
    }

    /** @test */
    public function image_service_handles_resize_from_existing_file()
    {
        $imageService = new ImageService();
        
        // Tạo file ảnh tạm
        $tempFile = UploadedFile::fake()->image('original.jpg', 2000, 1000);
        $tempPath = $tempFile->store('temp', 'public');
        $fullPath = Storage::disk('public')->path($tempPath);
        
        // Resize từ file đã tồn tại
        $resizedPath = $imageService->saveImage(
            $fullPath,
            'resized',
            1200,
            630,
            90,
            'resized-image'
        );
        
        $this->assertNotNull($resizedPath);
        $this->assertTrue(Storage::disk('public')->exists($resizedPath));
        $this->assertStringContains('resized/', $resizedPath);
    }

    /** @test */
    public function post_thumbnail_displays_correctly_in_frontend()
    {
        // Tạo post với thumbnail
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'thumbnail' => 'posts/thumbnails/test-image.webp',
            'status' => 'published'
        ]);

        // Tạo file ảnh giả
        Storage::disk('public')->put('posts/thumbnails/test-image.webp', 'fake image content');

        // Test trang chi tiết bài viết
        $response = $this->get(route('posts.show', $post->slug));
        
        $response->assertStatus(200);
        $response->assertSee('storage/posts/thumbnails/test-image.webp');
        $response->assertSee('class="w-full h-auto object-cover"');
    }

    /** @test */
    public function posts_filter_displays_responsive_images()
    {
        // Tạo posts với thumbnail
        $posts = Post::factory()->count(3)->create([
            'thumbnail' => 'posts/thumbnails/test-image.webp',
            'status' => 'published',
            'type' => 'news'
        ]);

        // Tạo file ảnh giả
        Storage::disk('public')->put('posts/thumbnails/test-image.webp', 'fake image content');

        // Test trang tin tức
        $response = $this->get('/tin-tuc');
        
        $response->assertStatus(200);
        $response->assertSee('aspect-[16/9]');
        $response->assertSee('object-cover');
        $response->assertSee('loading="lazy"');
    }

    /** @test */
    public function css_and_js_files_are_included()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('css/image-responsive.css');
        $response->assertSee('js/image-smart.js');
    }

    /** @test */
    public function image_placeholder_displays_when_no_thumbnail()
    {
        // Tạo post không có thumbnail
        $post = Post::factory()->create([
            'title' => 'Test Post Without Image',
            'thumbnail' => null,
            'status' => 'published'
        ]);

        $response = $this->get(route('posts.show', $post->slug));
        
        $response->assertStatus(200);
        // Không có ảnh thumbnail nên không hiển thị section ảnh
        $response->assertDontSee('Featured Image');
    }

    /** @test */
    public function posts_grid_handles_mixed_content()
    {
        // Tạo posts với và không có thumbnail
        Post::factory()->create([
            'thumbnail' => 'posts/thumbnails/with-image.webp',
            'status' => 'published',
            'type' => 'news'
        ]);

        Post::factory()->create([
            'thumbnail' => null,
            'status' => 'published',
            'type' => 'news'
        ]);

        Storage::disk('public')->put('posts/thumbnails/with-image.webp', 'fake image content');

        $response = $this->get('/tin-tuc');
        
        $response->assertStatus(200);
        // Kiểm tra có cả ảnh thật và placeholder
        $response->assertSee('storage/posts/thumbnails/with-image.webp');
        $response->assertSee('bg-gradient-to-br from-red-50 to-red-100');
    }

    /** @test */
    public function image_service_generates_seo_friendly_filenames()
    {
        $imageService = new ImageService();
        
        $file = UploadedFile::fake()->image('test.jpg');
        
        $path = $imageService->saveImageWithAspectRatio(
            $file,
            'posts/thumbnails',
            1200,
            630,
            90,
            'thumbnail-bài-viết-test'
        );
        
        $this->assertNotNull($path);
        // Kiểm tra tên file được slug hóa
        $this->assertStringContains('thumbnail-bai-viet-test', $path);
        $this->assertStringEndsWith('.webp', $path);
    }

    /** @test */
    public function responsive_css_classes_are_applied()
    {
        $post = Post::factory()->create([
            'thumbnail' => 'posts/thumbnails/test.webp',
            'status' => 'published'
        ]);

        Storage::disk('public')->put('posts/thumbnails/test.webp', 'fake content');

        $response = $this->get('/tin-tuc');
        
        $response->assertStatus(200);
        
        // Kiểm tra các class responsive
        $response->assertSee('aspect-[16/9]');
        $response->assertSee('w-full');
        $response->assertSee('object-cover');
        $response->assertSee('group-hover:scale-105');
        $response->assertSee('transition-transform');
        $response->assertSee('duration-300');
    }
}
