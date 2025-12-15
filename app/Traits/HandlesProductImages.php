<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HandlesProductImages
{
    /**
     * Tạo URL ảnh WebP với tên SEO-friendly cho sản phẩm
     *
     * @param object $product
     * @return string|null
     */
    public function getProductImageUrl($product): ?string
    {
        if (!$product || !$product->thumbnail) {
            return null;
        }

        // Tạo tên file SEO-friendly từ tên sản phẩm
        $seoName = Str::slug($product->name);
        $extension = pathinfo($product->thumbnail, PATHINFO_EXTENSION);

        // Chuyển đổi sang WebP nếu cần
        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
            $webpPath = str_replace('.' . $extension, '.webp', $product->thumbnail);
            $seoWebpPath = dirname($product->thumbnail) . '/' . $seoName . '.webp';

            // Kiểm tra file WebP có tồn tại không
            if (file_exists(storage_path('app/public/' . $seoWebpPath))) {
                return asset('storage/' . $seoWebpPath);
            } elseif (file_exists(storage_path('app/public/' . $webpPath))) {
                return asset('storage/' . $webpPath);
            }
        }

        // Fallback về ảnh gốc
        return asset('storage/' . $product->thumbnail);
    }

    /**
     * Tạo URL ảnh WebP với tên SEO-friendly cho ProductImage
     *
     * @param object $productImage
     * @param string $productName
     * @return string|null
     */
    public function getProductImageUrlFromImage($productImage, string $productName): ?string
    {
        if (!$productImage || !$productImage->image_link) {
            return null;
        }

        // Tạo tên file SEO-friendly từ tên sản phẩm
        $seoName = Str::slug($productName);
        $extension = pathinfo($productImage->image_link, PATHINFO_EXTENSION);

        // Chuyển đổi sang WebP nếu cần
        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
            $webpPath = str_replace('.' . $extension, '.webp', $productImage->image_link);
            $seoWebpPath = dirname($productImage->image_link) . '/' . $seoName . '.webp';

            // Kiểm tra file WebP có tồn tại không
            if (file_exists(storage_path('app/public/' . $seoWebpPath))) {
                return asset('storage/' . $seoWebpPath);
            } elseif (file_exists(storage_path('app/public/' . $webpPath))) {
                return asset('storage/' . $webpPath);
            }
        }

        // Fallback về ảnh gốc
        return asset('storage/' . $productImage->image_link);
    }

    /**
     * Lấy alt text cho ảnh sản phẩm
     *
     * @param object $productImage
     * @param string $productName
     * @param string|null $seoTitle
     * @return string
     */
    public function getProductImageAlt($productImage, string $productName, ?string $seoTitle = null): string
    {
        // Ưu tiên alt_text của ảnh
        if ($productImage && !empty($productImage->alt_text)) {
            return $productImage->alt_text;
        }

        // Sau đó là seo_title của sản phẩm
        if (!empty($seoTitle)) {
            return $seoTitle;
        }

        // Cuối cùng là tên sản phẩm
        return $productName;
    }

    /**
     * Tạo srcset cho responsive images
     *
     * @param string $imagePath
     * @param string $productName
     * @return string
     */
    public function getProductImageSrcset(string $imagePath, string $productName): string
    {
        $seoName = Str::slug($productName);
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $basePath = dirname($imagePath);
        $filename = pathinfo($imagePath, PATHINFO_FILENAME);

        $srcset = [];

        // Các kích thước responsive
        $sizes = [
            '400' => '400w',
            '600' => '600w',
            '800' => '800w',
            '1200' => '1200w'
        ];

        foreach ($sizes as $size => $descriptor) {
            // Tên file với kích thước
            $sizedFilename = $seoName . '-' . $size . 'w';

            // Kiểm tra WebP trước
            $webpPath = $basePath . '/' . $sizedFilename . '.webp';
            if (file_exists(storage_path('app/public/' . $webpPath))) {
                $srcset[] = asset('storage/' . $webpPath) . ' ' . $descriptor;
                continue;
            }

            // Fallback về định dạng gốc
            $originalPath = $basePath . '/' . $sizedFilename . '.' . $extension;
            if (file_exists(storage_path('app/public/' . $originalPath))) {
                $srcset[] = asset('storage/' . $originalPath) . ' ' . $descriptor;
            }
        }

        return implode(', ', $srcset);
    }

    /**
     * Kiểm tra xem file ảnh có tồn tại không
     *
     * @param string $path
     * @return bool
     */
    public function imageExists(string $path): bool
    {
        return file_exists(storage_path('app/public/' . $path));
    }

    /**
     * Tạo placeholder image local thay vì external URL
     * Trả về null để component tự xử lý fallback UI
     *
     * @param int $width
     * @param int $height
     * @param string $text
     * @param string $bgColor
     * @param string $textColor
     * @return string|null
     */
    public function getPlaceholderImage(
        int $width = 400,
        int $height = 400,
        string $text = 'No Image',
        string $bgColor = 'f3f4f6',
        string $textColor = '6b7280'
    ): ?string {
        // Trả về null để component tự xử lý fallback UI với CSS/HTML
        // Tránh sử dụng external placeholder service gây load liên tục
        return null;
    }
}
