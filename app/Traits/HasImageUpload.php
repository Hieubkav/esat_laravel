<?php

namespace App\Traits;

use Filament\Forms\Components\FileUpload;

trait HasImageUpload
{
    /**
     * Tạo FileUpload component với cấu hình chuẩn
     */
    protected static function createImageUpload(
        string $field = 'image_link',
        string $label = 'Hình ảnh',
        string $directory = 'uploads',
        int $width = 800,
        int $height = 600,
        int $maxSize = 5120,
        ?string $helperText = null,
        array $aspectRatios = ['16:9', '4:3', '1:1'],
        bool $required = false,
        bool $keepAspectRatio = false
    ): FileUpload {
        $upload = FileUpload::make($field)
            ->label($label)
            ->image()
            ->directory($directory)
            ->visibility('public')
            ->maxSize($maxSize)
            ->imageEditor()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']);

        // Thêm helper text nếu có
        if ($helperText) {
            $upload->helperText($helperText);
        } else {
            $upload->helperText('Chọn ảnh định dạng JPG, PNG hoặc WebP. Kích thước tối đa: ' . ($maxSize/1024) . 'MB');
        }

        // Thêm aspect ratios cho image editor
        if (!empty($aspectRatios)) {
            $upload->imageEditorAspectRatios($aspectRatios);
        }

        // Thêm required nếu cần
        if ($required) {
            $upload->required();
        }

        return $upload;
    }

    /**
     * Tạo FileUpload cho avatar/profile image
     */
    protected static function createAvatarUpload(
        string $field = 'avatar',
        string $label = 'Ảnh đại diện',
        string $directory = 'avatars',
        int $size = 300
    ): FileUpload {
        return self::createImageUpload(
            $field,
            $label,
            $directory,
            $size,
            $size,
            2048, // 2MB
            'Kích thước khuyến nghị: ' . $size . 'x' . $size . 'px (hình vuông)',
            ['1:1'],
            false,
            true
        );
    }

    /**
     * Tạo FileUpload cho thumbnail
     */
    protected static function createThumbnailUpload(
        string $field = 'thumbnail',
        string $label = 'Hình đại diện',
        string $directory = 'thumbnails',
        int $width = 400,
        int $height = 300
    ): FileUpload {
        return self::createImageUpload(
            $field,
            $label,
            $directory,
            $width,
            $height,
            3072, // 3MB
            'Kích thước khuyến nghị: ' . $width . 'x' . $height . 'px',
            ['4:3', '16:9', '1:1'],
            false,
            false
        );
    }

    /**
     * Tạo FileUpload cho banner/hero image
     */
    protected static function createBannerUpload(
        string $field = 'image_link',
        string $label = 'Hình ảnh Banner',
        string $directory = 'banners',
        int $width = 1920,
        int $height = 1080
    ): FileUpload {
        return self::createImageUpload(
            $field,
            $label,
            $directory,
            $width,
            $height,
            8192, // 8MB
            'Kích thước tối ưu: ' . $width . 'x' . $height . 'px (16:9) cho desktop, 800x450px cho mobile.',
            ['16:9'],
            true, // Required
            false // Không giữ tỷ lệ, crop theo kích thước
        );
    }

    /**
     * Tạo FileUpload cho logo với aspect ratio giữ nguyên
     */
    protected static function createLogoUpload(
        string $field = 'logo',
        string $label = 'Logo',
        string $directory = 'logos',
        int $maxWidth = 400,
        int $maxHeight = 200
    ): FileUpload {
        return self::createImageUpload(
            $field,
            $label,
            $directory,
            $maxWidth,
            $maxHeight,
            2048, // 2MB
            'Logo sẽ được resize giữ nguyên tỷ lệ. Kích thước tối đa: ' . $maxWidth . 'x' . $maxHeight . 'px',
            [], // Không giới hạn aspect ratio cho logo
            false,
            true // Giữ nguyên tỷ lệ
        );
    }

    /**
     * Tạo FileUpload cho gallery images trong RelationManager
     */
    protected function createGalleryUpload(
        string $field = 'image_link',
        string $label = 'Hình ảnh',
        string $directory = 'gallery',
        int $width = 800,
        int $height = 600
    ): FileUpload {
        return FileUpload::make($field)
            ->label($label)
            ->image()
            ->directory($directory)
            ->visibility('public')
            ->maxSize(5120)
            ->imageEditor()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->saveUploadedFileUsing(function ($file, $get, $livewire) use ($directory, $width, $height) {
                // Lấy tên từ owner record
                $customName = 'gallery';
                if (isset($livewire->ownerRecord)) {
                    if ($livewire->ownerRecord->title) {
                        $customName = 'gallery-' . $livewire->ownerRecord->title;
                    } elseif ($livewire->ownerRecord->name) {
                        $customName = 'gallery-' . $livewire->ownerRecord->name;
                    }
                }

                return \App\Actions\ConvertImageToWebp::run(
                    $file,
                    $directory,
                    $customName,
                    $width,
                    $height
                );
            })
            ->helperText('Ảnh sẽ được tự động chuyển sang định dạng WebP với chất lượng 95%');
    }
}
