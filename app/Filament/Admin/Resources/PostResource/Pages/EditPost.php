<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected $categoriesData = null;

    public function getTitle(): string
    {
        return 'Chỉnh sửa Bài viết';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewFrontend')
                ->label('Mở trang')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => route('posts.show', $this->getRecord()->slug))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Ở lại trang edit thay vì chuyển về list
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Bài viết đã được cập nhật thành công';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Đảm bảo thumbnail được load đúng cách cho FileUpload component
        if (!empty($data['thumbnail'])) {
            // Filament FileUpload expects an array for existing files
            $data['thumbnail'] = [$data['thumbnail']];
        }

        return $data;
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('✅ Lưu thành công!')
            ->body('Bài viết đã được cập nhật. Nội dung chi tiết (bao gồm ảnh GIF) đã được lưu và hiển thị.')
            ->duration(5000)
            ->actions([
                \Filament\Notifications\Actions\Action::make('refresh')
                    ->label('🔄 Refresh trang')
                    ->button()
                    ->action(fn () => redirect()->to(request()->url()))
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ghi nhận người chỉnh sửa bài viết
        $data['updated_by'] = auth()->id();

        // Debug logging
        Log::info('EditPost - mutateFormDataBeforeSave', [
            'post_id' => $this->getRecord()->id,
            'data_keys' => array_keys($data),
            'thumbnail' => $data['thumbnail'] ?? 'NULL',
            'title' => $data['title'] ?? 'NULL'
        ]);

        // Xử lý thumbnail từ array về string nếu cần
        if (isset($data['thumbnail']) && is_array($data['thumbnail'])) {
            $data['thumbnail'] = !empty($data['thumbnail']) ? $data['thumbnail'][0] : null;
        }

        // Loại bỏ categories khỏi data vì nó sẽ được xử lý trong afterSave
        if (isset($data['categories'])) {
            $this->categoriesData = $data['categories'];
            unset($data['categories']);
        }

        // Tự động tạo content từ content_builder
        if (!empty($data['content_builder'])) {
            $data['content'] = $this->extractContentFromBuilder($data['content_builder']);
        } else {
            // Nếu không có content_builder, đặt content rỗng
            $data['content'] = '';
        }

        // Tự động tạo SEO title nếu trống
        if (empty($data['seo_title']) && !empty($data['title'])) {
            $data['seo_title'] = PostResource::generateSeoTitle($data['title']);
        }

        // Tự động tạo SEO description nếu trống từ content đã được tạo
        if (empty($data['seo_description']) && !empty($data['content'])) {
            $data['seo_description'] = PostResource::generateSeoDescription($data['content']);
        }

        // Tự động copy thumbnail làm OG image nếu OG image trống
        if (empty($data['og_image_link']) && !empty($data['thumbnail'])) {
            $data['og_image_link'] = $data['thumbnail'];
        }

        Log::info('EditPost - mutateFormDataBeforeSave - After processing', [
            'post_id' => $this->getRecord()->id,
            'final_data_keys' => array_keys($data),
            'final_thumbnail' => $data['thumbnail'] ?? 'NULL'
        ]);

        return $data;
    }

    protected function afterSave(): void
    {
        // Đồng bộ categories sau khi lưu record
        if ($this->categoriesData !== null) {
            $this->getRecord()->categories()->sync($this->categoriesData);
        }

        // Log sau khi save để kiểm tra
        Log::info('EditPost - afterSave', [
            'post_id' => $this->getRecord()->id,
            'thumbnail' => $this->getRecord()->thumbnail,
            'updated_at' => $this->getRecord()->updated_at
        ]);
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        // Force refresh thumbnail field để đảm bảo hiển thị đúng
        $record = $this->getRecord();
        if ($record && $record->thumbnail) {
            Log::info('EditPost - fillForm - Force refresh thumbnail', [
                'post_id' => $record->id,
                'thumbnail' => $record->thumbnail
            ]);
        }
    }

    /**
     * Trích xuất nội dung text từ content_builder
     */
    private function extractContentFromBuilder(array $contentBuilder): string
    {
        $content = '';

        foreach ($contentBuilder as $block) {
            if (!isset($block['type']) || !isset($block['data'])) {
                continue;
            }

            switch ($block['type']) {
                case 'paragraph':
                    if (isset($block['data']['content'])) {
                        $content .= strip_tags($block['data']['content']) . "\n\n";
                    }
                    break;

                case 'heading':
                    if (isset($block['data']['content'])) {
                        $content .= strip_tags($block['data']['content']) . "\n\n";
                    }
                    break;

                case 'list':
                    if (isset($block['data']['items']) && is_array($block['data']['items'])) {
                        foreach ($block['data']['items'] as $item) {
                            $content .= '- ' . strip_tags($item) . "\n";
                        }
                        $content .= "\n";
                    }
                    break;

                case 'quote':
                    if (isset($block['data']['content'])) {
                        $content .= '"' . strip_tags($block['data']['content']) . '"' . "\n\n";
                    }
                    break;

                case 'image':
                    if (isset($block['data']['caption'])) {
                        $content .= strip_tags($block['data']['caption']) . "\n\n";
                    }
                    break;

                case 'video':
                    if (isset($block['data']['caption'])) {
                        $content .= strip_tags($block['data']['caption']) . "\n\n";
                    }
                    break;
            }
        }

        return trim($content);
    }


}