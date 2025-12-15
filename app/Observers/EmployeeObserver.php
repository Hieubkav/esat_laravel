<?php

namespace App\Observers;

use App\Models\Employee;
use App\Services\ImageService;
use App\Services\QrCodeService;
use App\Traits\HandlesFileObserver;
use Illuminate\Support\Facades\Log;

class EmployeeObserver
{
    use HandlesFileObserver;

    protected $imageService;
    protected $qrCodeService;

    public function __construct(ImageService $imageService, QrCodeService $qrCodeService)
    {
        $this->imageService = $imageService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // Tạo QR code tự động nếu chưa có
        $this->generateQrCodeIfNeeded($employee);
    }

    /**
     * Handle the Employee "updating" event.
     */
    public function updating(Employee $employee): void
    {
        $modelClass = get_class($employee);
        $modelId = $employee->id;

        // Lưu image_link cũ
        if ($employee->isDirty('image_link')) {
            $this->storeOldFile($modelClass, $modelId, 'image_link', $employee->getOriginal('image_link'));
        }

        // Lưu qr_code cũ
        if ($employee->isDirty('qr_code')) {
            $this->storeOldFile($modelClass, $modelId, 'qr_code', $employee->getOriginal('qr_code'));
        }
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        $modelClass = get_class($employee);
        $modelId = $employee->id;

        // Lấy và xóa image_link cũ
        $oldImage = $this->getAndDeleteOldFile($modelClass, $modelId, 'image_link');
        if ($oldImage) {
            $this->imageService->deleteImage($oldImage);
        }

        // Lấy và xóa qr_code cũ
        $oldQrCode = $this->getAndDeleteOldFile($modelClass, $modelId, 'qr_code');
        if ($oldQrCode) {
            $this->imageService->deleteImage($oldQrCode);
        }

        // Tạo QR code mới nếu slug hoặc name thay đổi
        if ($employee->wasChanged('slug') || $employee->wasChanged('name')) {
            $this->generateQrCodeIfNeeded($employee);
        }
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        // Xóa hình ảnh chính khi xóa nhân viên
        if ($employee->image_link) {
            $this->imageService->deleteImage($employee->image_link);
        }

        // Xóa QR code khi xóa nhân viên
        if ($employee->qr_code) {
            $this->imageService->deleteImage($employee->qr_code);
        }

        // Xóa tất cả hình ảnh liên quan trong EmployeeImage
        foreach ($employee->images as $employeeImage) {
            if ($employeeImage->image_link) {
                $this->imageService->deleteImage($employeeImage->image_link);
            }
        }
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }

    /**
     * Tạo QR code nếu cần thiết
     */
    private function generateQrCodeIfNeeded(Employee $employee): void
    {
        if (empty($employee->qr_code) && !empty($employee->slug)) {
            try {
                $profileUrl = route('employee.profile', $employee->slug);
                $qrCodePath = $this->qrCodeService->generateEmployeeQrCode($profileUrl, $employee->name);

                if ($qrCodePath) {
                    $employee->updateQuietly(['qr_code' => $qrCodePath]);
                }
            } catch (\Exception $e) {
                Log::error('Lỗi tạo QR code cho nhân viên ' . $employee->name . ': ' . $e->getMessage());
            }
        }
    }
}
