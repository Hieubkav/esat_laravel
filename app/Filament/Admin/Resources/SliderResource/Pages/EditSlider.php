<?php

namespace App\Filament\Admin\Resources\SliderResource\Pages;

use App\Filament\Admin\Resources\SliderResource;
use App\Services\ImageService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSlider extends EditRecord
{
    protected static string $resource = SliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('optimize_image')
                ->label('Chỉnh ảnh thành 16:9')
                ->icon('heroicon-o-photo')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Tối ưu ảnh thành tỷ lệ 16:9')
                ->modalDescription('Ảnh sẽ được resize và crop để đạt tỷ lệ 16:9 tối ưu cho slider banner. Thao tác này không thể hoàn tác.')
                ->modalSubmitActionLabel('Chỉnh ảnh')
                ->action(function () {
                    $record = $this->getRecord();

                    if (!$record->image_link) {
                        Notification::make()
                            ->title('Lỗi')
                            ->body('Không tìm thấy ảnh để tối ưu.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $imageService = app(ImageService::class);
                    $newImagePath = $imageService->resizeToSixteenNine(
                        $record->image_link,
                        $record->title ? "hero-banner-{$record->title}" : null
                    );

                    if ($newImagePath) {
                        $record->update(['image_link' => $newImagePath]);

                        Notification::make()
                            ->title('Thành công')
                            ->body('Ảnh đã được tối ưu thành tỷ lệ 16:9.')
                            ->success()
                            ->send();

                        // Refresh trang để hiển thị ảnh mới
                        $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                    } else {
                        Notification::make()
                            ->title('Lỗi')
                            ->body('Không thể tối ưu ảnh. Vui lòng thử lại.')
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn () => $this->getRecord()?->image_link),

            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }

    public function getTitle(): string
    {
        return 'Chỉnh sửa Slider';
    }

    protected function getRedirectUrl(): string
    {
        // Ở lại trang edit sau khi save
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Slider đã được cập nhật thành công';
    }
}
