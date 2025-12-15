<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Lưu hình ảnh tải lên với định dạng WebP và tối ưu hóa
     *
     * @param UploadedFile|string $image Ảnh tải lên hoặc đường dẫn ảnh
     * @param string $directory Thư mục lưu trong storage
     * @param int $width Chiều rộng (0 để giữ nguyên)
     * @param int $height Chiều cao (0 để giữ nguyên)
     * @param int $quality Chất lượng ảnh (1-100)
     * @param string|null $customName Tên file tùy chỉnh cho SEO
     * @return string|null Đường dẫn đến ảnh đã lưu
     */
    public function saveImage($image, string $directory, int $width = 0, int $height = 0, int $quality = 80, ?string $customName = null): ?string
    {
        // Nếu không có ảnh, trả về null
        if (!$image) {
            return null;
        }

        // Khởi tạo ImageManager với driver GD
        $manager = new ImageManager(new Driver());

        // Nếu là UploadedFile (file mới upload)
        if ($image instanceof UploadedFile) {
            try {
                // Kiểm tra file có hợp lệ không
                if (!$image->isValid()) {
                    Log::error('ImageService: Invalid file upload');
                    return null;
                }

                // Kiểm tra file path có tồn tại không
                $realPath = $image->getRealPath();
                if (!$realPath || !file_exists($realPath)) {
                    Log::error('ImageService: File does not exist at path: ' . ($realPath ?? 'null'));
                    return null;
                }

                // Tạo tên file mới với định dạng webp
                $filename = $this->generateFilename($customName) . '.webp';

                // Đọc hình ảnh từ file tạm
                $img = $manager->read($realPath);

                // Resize nếu có kích thước chỉ định
                if ($width > 0 && $height > 0) {
                    $img->resize($width, $height);
                } elseif ($width > 0) {
                    $img->resize(width: $width);
                } elseif ($height > 0) {
                    $img->resize(height: $height);
                }

                // Chuyển đổi sang WebP và tối ưu hóa
                $encodedImage = $img->toWebp($quality);

                // Lưu vào storage disk 'public'
                $filePath = $directory . '/' . $filename;
                Storage::disk('public')->put($filePath, $encodedImage);

                // Trả về đường dẫn tương đối
                return $filePath;
            } catch (\Exception $e) {
                Log::error('ImageService error: ' . $e->getMessage(), [
                    'file_name' => $image->getClientOriginalName() ?? 'unknown',
                    'file_size' => $image->getSize() ?? 'unknown',
                    'directory' => $directory
                ]);

                // Fallback: lưu file gốc
                try {
                    return $image->store($directory, 'public');
                } catch (\Exception $fallbackError) {
                    Log::error('ImageService fallback error: ' . $fallbackError->getMessage());
                    return null;
                }
            }
        }

        // Nếu là đường dẫn file tuyệt đối (để resize ảnh đã tồn tại)
        elseif (is_string($image) && file_exists($image)) {
            // Tạo tên file mới
            $filename = $this->generateFilename($customName) . '.webp';

            // Đọc ảnh từ đường dẫn
            $img = $manager->read($image);

            // Resize nếu có kích thước được chỉ định
            if ($width > 0 && $height > 0) {
                $img->resize($width, $height);
            } elseif ($width > 0) {
                $img->resize(width: $width);
            } elseif ($height > 0) {
                $img->resize(height: $height);
            }

            // Chuyển đổi sang WebP và tối ưu hóa
            $encodedImage = $img->toWebp($quality);

            // Lưu vào storage disk 'public'
            $filePath = $directory . '/' . $filename;
            Storage::disk('public')->put($filePath, $encodedImage);

            // Trả về đường dẫn tương đối
            return $filePath;
        }

        // Nếu là đường dẫn chuỗi (đã lưu trước đó trong storage)
        elseif (is_string($image) && Storage::disk('public')->exists($image)) {
            return $image; // Giữ nguyên nếu đã tồn tại
        }

        return null;
    }

    /**
     * Xóa hình ảnh từ storage
     *
     * @param string|null $imagePath Đường dẫn hình ảnh cần xóa
     * @return bool
     */
    public function deleteImage(?string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }

        if (Storage::disk('public')->exists($imagePath)) {
            return Storage::disk('public')->delete($imagePath);
        }

        return false;
    }

    /**
     * Lưu hình ảnh với giữ nguyên tỷ lệ (aspect ratio) - dành cho logo
     *
     * @param UploadedFile|string $image Ảnh tải lên hoặc đường dẫn ảnh
     * @param string $directory Thư mục lưu trong storage
     * @param int $maxWidth Chiều rộng tối đa
     * @param int $maxHeight Chiều cao tối đa
     * @param int $quality Chất lượng ảnh (1-100)
     * @param string|null $customName Tên file tùy chỉnh cho SEO
     * @return string|null Đường dẫn đến ảnh đã lưu
     */
    public function saveImageWithAspectRatio($image, string $directory, int $maxWidth = 400, int $maxHeight = 200, int $quality = 80, ?string $customName = null): ?string
    {
        // Nếu không có ảnh, trả về null
        if (!$image) {
            return null;
        }

        // Khởi tạo ImageManager với driver GD
        $manager = new ImageManager(new Driver());

        // Nếu là UploadedFile (file mới upload)
        if ($image instanceof UploadedFile) {
            // Tạo tên file mới với định dạng webp
            $filename = $this->generateFilename($customName) . '.webp';

            // Đọc hình ảnh từ file tạm
            $img = $manager->read($image->getRealPath());

            // Resize giữ nguyên tỷ lệ - chỉ resize nếu ảnh lớn hơn kích thước tối đa
            $originalWidth = $img->width();
            $originalHeight = $img->height();

            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                // Tính tỷ lệ scale để vừa trong khung mà không méo
                $scaleWidth = $maxWidth / $originalWidth;
                $scaleHeight = $maxHeight / $originalHeight;
                $scale = min($scaleWidth, $scaleHeight);

                $newWidth = (int)($originalWidth * $scale);
                $newHeight = (int)($originalHeight * $scale);

                $img->resize($newWidth, $newHeight);
            }

            // Chuyển đổi sang WebP và tối ưu hóa
            $encodedImage = $img->toWebp($quality);

            // Lưu vào storage disk 'public'
            $filePath = $directory . '/' . $filename;
            Storage::disk('public')->put($filePath, $encodedImage);

            // Trả về đường dẫn tương đối
            return $filePath;
        }

        // Nếu là đường dẫn file tuyệt đối (để resize ảnh đã tồn tại)
        elseif (is_string($image) && file_exists($image)) {
            // Tạo tên file mới
            $filename = $this->generateFilename($customName) . '.webp';

            // Đọc ảnh từ đường dẫn
            $img = $manager->read($image);

            // Resize giữ nguyên tỷ lệ - chỉ resize nếu ảnh lớn hơn kích thước tối đa
            $originalWidth = $img->width();
            $originalHeight = $img->height();

            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                // Tính tỷ lệ scale để vừa trong khung mà không méo
                $scaleWidth = $maxWidth / $originalWidth;
                $scaleHeight = $maxHeight / $originalHeight;
                $scale = min($scaleWidth, $scaleHeight);

                $newWidth = (int)($originalWidth * $scale);
                $newHeight = (int)($originalHeight * $scale);

                $img->resize($newWidth, $newHeight);
            }

            // Chuyển đổi sang WebP và tối ưu hóa
            $encodedImage = $img->toWebp($quality);

            // Lưu vào storage disk 'public'
            $filePath = $directory . '/' . $filename;
            Storage::disk('public')->put($filePath, $encodedImage);

            // Trả về đường dẫn tương đối
            return $filePath;
        }

        // Nếu là đường dẫn chuỗi (đã lưu trước đó trong storage)
        elseif (is_string($image) && Storage::disk('public')->exists($image)) {
            return $image; // Giữ nguyên nếu đã tồn tại
        }

        return null;
    }

    /**
     * Lưu file cho RichEditor - giữ nguyên GIF, chuyển các format khác thành WebP
     *
     * @param UploadedFile $file File upload
     * @param string $directory Thư mục lưu
     * @param int $maxWidth Chiều rộng tối đa
     * @param int $quality Chất lượng ảnh
     * @param string|null $customName Tên file tùy chỉnh
     * @return string|null Đường dẫn đến file đã lưu
     */
    public function saveRichEditorFile($file, string $directory, int $maxWidth = 1200, int $quality = 85, ?string $customName = null): ?string
    {
        Log::info('RichEditor file upload started', [
            'file_type' => get_class($file),
            'directory' => $directory,
            'custom_name' => $customName
        ]);

        if (!$file instanceof UploadedFile) {
            Log::error('File is not UploadedFile instance');
            return null;
        }

        $originalExtension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        Log::info('File details', [
            'original_name' => $file->getClientOriginalName(),
            'extension' => $originalExtension,
            'mime_type' => $mimeType,
            'size' => $file->getSize()
        ]);

        // Giữ nguyên GIF để bảo toàn animation
        if ($originalExtension === 'gif' || $mimeType === 'image/gif') {
            $filename = $this->generateFilename($customName) . '.gif';
            $filePath = $directory . '/' . $filename;

            Log::info('Saving GIF file', ['path' => $filePath, 'filename' => $filename]);

            // Lưu trực tiếp file GIF mà không xử lý
            Storage::disk('public')->put($filePath, file_get_contents($file->getRealPath()));

            Log::info('GIF saved successfully', ['result' => $filePath]);
            return $filePath;
        }

        // Với các format khác, chuyển thành WebP để tối ưu
        $manager = new ImageManager(new Driver());
        $filename = $this->generateFilename($customName) . '.webp';

        try {
            Log::info('Processing image to WebP', ['filename' => $filename]);

            $img = $manager->read($file->getRealPath());

            // Resize nếu ảnh quá lớn
            if ($maxWidth > 0 && $img->width() > $maxWidth) {
                Log::info('Resizing image', ['original_width' => $img->width(), 'new_width' => $maxWidth]);
                $img->resize(width: $maxWidth);
            }

            // Chuyển đổi sang WebP
            $encodedImage = $img->toWebp($quality);

            // Lưu vào storage
            $filePath = $directory . '/' . $filename;
            Storage::disk('public')->put($filePath, $encodedImage);

            Log::info('WebP saved successfully', ['result' => $filePath]);
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error processing image', ['error' => $e->getMessage()]);

            // Nếu có lỗi, lưu file gốc
            $originalFilename = $this->generateFilename($customName) . '.' . $originalExtension;
            $filePath = $directory . '/' . $originalFilename;
            Storage::disk('public')->put($filePath, file_get_contents($file->getRealPath()));

            Log::info('Original file saved as fallback', ['result' => $filePath]);
            return $filePath;
        }
    }

    /**
     * Resize ảnh thành tỷ lệ 16:9 tối ưu cho slider banner
     *
     * @param string $imagePath Đường dẫn ảnh hiện tại trong storage
     * @param string|null $customName Tên file tùy chỉnh
     * @return string|null Đường dẫn đến ảnh đã resize
     */
    public function resizeToSixteenNine(string $imagePath, ?string $customName = null): ?string
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return null;
        }

        $manager = new ImageManager(new Driver());

        try {
            // Đọc ảnh từ storage
            $fullPath = Storage::disk('public')->path($imagePath);
            $img = $manager->read($fullPath);

            // Kích thước mục tiêu 16:9
            $targetWidth = 1920;
            $targetHeight = 1080;

            // Resize và crop để đạt tỷ lệ 16:9 chính xác
            $img->cover($targetWidth, $targetHeight);

            // Tạo tên file mới
            $filename = $this->generateFilename($customName ?: 'slider-16-9') . '.webp';

            // Chuyển đổi sang WebP với chất lượng cao
            $encodedImage = $img->toWebp(85);

            // Lưu vào cùng thư mục
            $directory = dirname($imagePath);
            $newFilePath = $directory . '/' . $filename;
            Storage::disk('public')->put($newFilePath, $encodedImage);

            // Xóa ảnh cũ nếu khác tên
            $oldFilename = basename($imagePath);
            if ($oldFilename !== $filename) {
                Storage::disk('public')->delete($imagePath);
            }

            return $newFilePath;
        } catch (\Exception $e) {
            Log::error('Error resizing image to 16:9', [
                'image_path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Tạo tên file SEO-friendly
     *
     * @param string|null $customName Tên tùy chỉnh
     * @return string
     */
    private function generateFilename(?string $customName = null): string
    {
        if ($customName) {
            // Chuyển đổi tên thành slug SEO-friendly
            $slug = Str::slug($customName, '-');
            // Thêm timestamp để tránh trùng lặp
            return $slug . '-' . time();
        }

        // Fallback về random string nếu không có tên tùy chỉnh
        return Str::random(20);
    }
}