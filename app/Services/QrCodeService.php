<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Tạo QR code cho nhân viên
     *
     * @param string $url URL trang thông tin nhân viên
     * @param string $employeeName Tên nhân viên để tạo tên file
     * @return string|null Đường dẫn đến file QR code đã lưu
     */
    public function generateEmployeeQrCode(string $url, string $employeeName): ?string
    {
        try {
            // Tạo tên file SEO-friendly
            $fileName = 'qr-' . Str::slug($employeeName) . '-' . time() . '.svg';
            $directory = 'employees/qr-codes';
            $filePath = $directory . '/' . $fileName;

            // Tạo QR code với format SVG để tránh lỗi ImageMagick
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->margin(2)
                ->errorCorrection('H')
                ->encoding('UTF-8')
                ->generate($url);

            // Đảm bảo thư mục tồn tại
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Lưu vào storage
            Storage::disk('public')->put($filePath, $qrCode);

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Lỗi tạo QR code: ' . $e->getMessage());
            echo "Error: " . $e->getMessage() . "\n";
            return null;
        }
    }

    /**
     * Xóa QR code cũ
     *
     * @param string|null $qrCodePath Đường dẫn QR code cần xóa
     * @return bool
     */
    public function deleteQrCode(?string $qrCodePath): bool
    {
        if (!$qrCodePath) {
            return false;
        }

        try {
            if (Storage::disk('public')->exists($qrCodePath)) {
                return Storage::disk('public')->delete($qrCodePath);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi xóa QR code: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo QR code trực tiếp cho download
     *
     * @param string $url URL cần tạo QR code
     * @return string QR code dạng SVG
     */
    public function generateQrCodeForDownload(string $url): string
    {
        return QrCode::format('svg')
            ->size(400)
            ->margin(3)
            ->errorCorrection('H')
            ->encoding('UTF-8')
            ->generate($url);
    }
}
