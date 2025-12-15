<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\ImageService;
use App\Filament\Admin\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostObserver
{
    protected $imageService;
    protected $oldThumbnails = []; // Array to store old thumbnails by post ID
    protected $oldOgImages = []; // Array to store old OG images by post ID

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the Post "creating" event.
     */
    public function creating(Post $post): void
    {
        // Tự động tạo SEO title nếu trống
        if (empty($post->seo_title) && !empty($post->title)) {
            $post->seo_title = PostResource::generateSeoTitle($post->title);
        }

        // Tự động tạo SEO description nếu trống
        if (empty($post->seo_description) && !empty($post->content)) {
            $post->seo_description = PostResource::generateSeoDescription($post->content);
        }

        // Tự động copy thumbnail làm OG image nếu OG image trống
        if (empty($post->og_image_link) && !empty($post->thumbnail)) {
            $post->og_image_link = $post->thumbnail;
        }
    }

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        // Hình ảnh đã được xử lý trong form Filament
    }

    /**
     * Handle the Post "updating" event.
     */
    public function updating(Post $post): void
    {
        // Tự động tạo SEO title nếu trống
        if (empty($post->seo_title) && !empty($post->title)) {
            $post->seo_title = PostResource::generateSeoTitle($post->title);
        }

        // Tự động tạo SEO description nếu trống
        if (empty($post->seo_description) && !empty($post->content)) {
            $post->seo_description = PostResource::generateSeoDescription($post->content);
        }

        // Tự động copy thumbnail làm OG image nếu OG image trống
        if (empty($post->og_image_link) && !empty($post->thumbnail)) {
            $post->og_image_link = $post->thumbnail;
        }

        // Xử lý ảnh trong RichEditor content
        if ($post->isDirty('content')) {
            $this->handleRichEditorImages($post);
        }

        // Xử lý xóa file cũ
        if ($post->isDirty('thumbnail') && $post->getOriginal('thumbnail')) {
            // Lưu đường dẫn hình ảnh cũ vào mảng tạm thời để xóa sau
            $this->oldThumbnails[$post->id] = $post->getOriginal('thumbnail');
        }

        if ($post->isDirty('og_image_link') && $post->getOriginal('og_image_link')) {
            // Lưu đường dẫn OG image cũ vào mảng tạm thời để xóa sau
            $this->oldOgImages[$post->id] = $post->getOriginal('og_image_link');
        }
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        // Nếu có hình ảnh thumbnail cũ cần xóa
        if (isset($this->oldThumbnails[$post->id])) {
            $this->imageService->deleteImage($this->oldThumbnails[$post->id]);
            unset($this->oldThumbnails[$post->id]);
        }

        // Nếu có OG image cũ cần xóa
        if (isset($this->oldOgImages[$post->id])) {
            $this->imageService->deleteImage($this->oldOgImages[$post->id]);
            unset($this->oldOgImages[$post->id]);
        }
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        // Xóa hình ảnh thumbnail khi xóa bài viết
        if ($post->thumbnail) {
            $this->imageService->deleteImage($post->thumbnail);
        }

        // Xóa OG image khi xóa bài viết
        if ($post->og_image_link) {
            $this->imageService->deleteImage($post->og_image_link);
        }

        // Xóa tất cả ảnh trong RichEditor content
        $this->deleteRichEditorImages($post->content);

        // Xóa tất cả hình ảnh liên quan trong PostImage
        foreach ($post->images as $postImage) {
            if ($postImage->image_link) {
                $this->imageService->deleteImage($postImage->image_link);
            }
        }
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }

    /**
     * Xử lý ảnh trong RichEditor khi content thay đổi
     */
    private function handleRichEditorImages(Post $post): void
    {
        $oldContent = $post->getOriginal('content');
        $newContent = $post->content;

        // Tìm các ảnh trong content cũ và mới
        $oldImages = $this->extractImagesFromContent($oldContent);
        $newImages = $this->extractImagesFromContent($newContent);

        // Tìm ảnh bị xóa (có trong old nhưng không có trong new)
        $deletedImages = array_diff($oldImages, $newImages);

        // Xóa các ảnh không còn sử dụng
        foreach ($deletedImages as $imagePath) {
            $this->deleteImageFromStorage($imagePath);
        }
    }

    /**
     * Xóa tất cả ảnh trong RichEditor content
     */
    private function deleteRichEditorImages(?string $content): void
    {
        $images = $this->extractImagesFromContent($content);
        foreach ($images as $imagePath) {
            $this->deleteImageFromStorage($imagePath);
        }
    }

    /**
     * Trích xuất đường dẫn ảnh từ HTML content
     */
    private function extractImagesFromContent(?string $content): array
    {
        if (empty($content)) {
            return [];
        }

        $images = [];

        // Tìm tất cả thẻ img trong content
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $src) {
                // Chỉ xử lý ảnh từ storage local
                if (strpos($src, '/storage/posts/content/') !== false) {
                    // Lấy đường dẫn tương đối từ storage
                    $relativePath = str_replace(asset('storage/'), '', $src);
                    $images[] = $relativePath;
                }
            }
        }

        return array_unique($images);
    }

    /**
     * Xóa ảnh từ storage
     */
    private function deleteImageFromStorage(string $imagePath): void
    {
        try {
            // Đường dẫn đầy đủ trong storage
            $fullPath = 'public/' . $imagePath;

            if (Storage::exists($fullPath)) {
                Storage::delete($fullPath);
                Log::info("Deleted rich editor image: {$imagePath}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to delete rich editor image: {$imagePath}. Error: " . $e->getMessage());
        }
    }
}
