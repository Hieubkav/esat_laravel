<?php

namespace App\Filament\Admin\Resources\EmployeeResource\Pages;

use App\Filament\Admin\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\QrCodeService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewProfile')
                ->label('Xem trang nhân viên')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->url(fn () => route('employee.profile', $this->record->slug))
                ->openUrlInNewTab()
                ->visible(fn () => !empty($this->record->slug)),

            Actions\Action::make('generateQrCode')
                ->label('Tạo QR Code')
                ->icon('heroicon-o-qr-code')
                ->color('info')
                ->action(function () {
                    $qrCodeService = app(QrCodeService::class);
                    $profileUrl = route('employee.profile', $this->record->slug);
                    $qrCodePath = $qrCodeService->generateEmployeeQrCode($profileUrl, $this->record->name);

                    if ($qrCodePath) {
                        // Xóa QR code cũ nếu có
                        if ($this->record->qr_code) {
                            $qrCodeService->deleteQrCode($this->record->qr_code);
                        }

                        // Cập nhật đường dẫn QR code mới
                        $this->record->update(['qr_code' => $qrCodePath]);

                        Notification::make()
                            ->title('Tạo QR Code thành công!')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Lỗi tạo QR Code!')
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Tạo QR Code mới')
                ->modalDescription('Bạn có chắc chắn muốn tạo QR Code mới? QR Code cũ sẽ bị thay thế.')
                ->visible(fn () => !empty($this->record->slug)),

            Actions\Action::make('downloadQrCode')
                ->label('Tải QR Code')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn () => route('employee.qr-download', $this->record->slug))
                ->openUrlInNewTab()
                ->visible(fn () => !empty($this->record->slug)),

            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }

    public function getTitle(): string
    {
        return 'Chỉnh sửa Nhân viên';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Nhân viên đã được cập nhật thành công';
    }
}
