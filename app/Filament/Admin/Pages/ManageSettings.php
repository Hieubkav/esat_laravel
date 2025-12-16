<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use App\Constants\NavigationGroups;
use App\Traits\HasPageRoleBasedAccess;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms, HasPageRoleBasedAccess;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = NavigationGroups::WEBSITE_SETTINGS;

    protected static string $view = 'filament.admin.pages.manage-settings';

    protected static ?string $title = 'Cài đặt chung';

    protected static ?string $navigationLabel = 'Cài đặt chung';

    protected static ?int $navigationSort = 43;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::first() ?? new Setting();
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin website')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Tên website')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('hotline')
                            ->label('Hotline')
                            ->required()
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('slogan')
                            ->label('Slogan')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Logo và Favicon')
                    ->schema([
                        FileUpload::make('logo_link')
                            ->label('Logo')
                            ->helperText('Tỷ lệ tối ưu: 2:1 (400x200px). Tránh logo vuông hoặc quá dài.')
                            ->image()
                            ->directory('settings/logos')
                            ->visibility('public')
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth(400)
                            ->imageResizeTargetHeight(200)
                            ->imageEditor()
                            ->saveUploadedFileUsing(function ($file, $get) {
                                $imageService = app(\App\Services\ImageService::class);
                                $siteName = $get('site_name') ?? 'website';
                                return $imageService->saveImageWithAspectRatio(
                                    $file,
                                    'settings/logos',
                                    400,   // max width
                                    200,   // max height
                                    100,   // quality
                                    "logo-{$siteName}" // SEO-friendly name
                                );
                            })
                            ->columnSpan(1),

                        FileUpload::make('favicon_link')
                            ->label('Favicon')
                            ->helperText('Kích thước: 32x32px (vuông). Chỉ dùng icon, không text.')
                            ->image()
                            ->directory('settings/favicons')
                            ->visibility('public')
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth(32)
                            ->imageResizeTargetHeight(32)
                            ->imageEditor()
                            ->saveUploadedFileUsing(function ($file, $get) {
                                $imageService = app(\App\Services\ImageService::class);
                                $siteName = $get('site_name') ?? 'website';
                                return $imageService->saveImageWithAspectRatio(
                                    $file,
                                    'settings/favicons',
                                    32,    // max width
                                    32,    // max height
                                    100,   // quality
                                    "favicon-{$siteName}" // SEO-friendly name
                                );
                            })
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Liên kết mạng xã hội')
                    ->schema([
                        TextInput::make('youtube_link')
                            ->label('YouTube')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('zalo_link')
                            ->label('Zalo')
                            ->maxLength(255),
                        TextInput::make('facebook_link')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('tiktok_link')
                            ->label('TikTok')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('messenger_link')
                            ->label('Messenger')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('SEO và Thông tin khác')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('Tiêu đề SEO')
                            ->maxLength(255),
                        Textarea::make('seo_description')
                            ->label('Mô tả SEO')
                            ->rows(3)
                            ->maxLength(255),
                        FileUpload::make('og_image_link')
                            ->label('Hình ảnh OG (Social Media)')
                            ->helperText('Kích thước: 1200x630px. Logo ở góc, text ở giữa.')
                            ->image()
                            ->directory('settings/og-images')
                            ->visibility('public')
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth(1200)
                            ->imageResizeTargetHeight(630)
                            ->imageEditor()
                            ->saveUploadedFileUsing(function ($file, $get) {
                                $imageService = app(\App\Services\ImageService::class);
                                $siteName = $get('site_name') ?? 'website';
                                return $imageService->saveImageWithAspectRatio(
                                    $file,
                                    'settings/og-images',
                                    1200,  // max width
                                    630,   // max height
                                    85,    // quality
                                    "og-image-{$siteName}" // SEO-friendly name
                                );
                            }),
                        FileUpload::make('placeholder_image')
                            ->label('Ảnh tạm thời (Placeholder)')
                            ->helperText('Ảnh hiển thị khi không có ảnh chính cho sản phẩm, bài viết, nhân viên... (sẽ giữ nguyên tỷ lệ)')
                            ->image()
                            ->directory('settings/placeholders')
                            ->visibility('public')
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth(400)
                            ->imageResizeTargetHeight(400)
                            ->imageEditor()
                            ->saveUploadedFileUsing(function ($file, $get) {
                                $imageService = app(\App\Services\ImageService::class);
                                $siteName = $get('site_name') ?? 'website';
                                return $imageService->saveImageWithAspectRatio(
                                    $file,
                                    'settings/placeholders',
                                    400,   // max width
                                    400,   // max height
                                    90,    // quality
                                    "placeholder-{$siteName}" // SEO-friendly name
                                );
                            }),
                        Textarea::make('address')
                            ->label('Địa chỉ')
                            ->rows(3)
                            ->maxLength(500),
                        TextInput::make('working_hours')
                            ->label('Giờ làm việc')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = Setting::first();

        if ($settings) {
            $settings->update($data);
        } else {
            Setting::create($data);
        }

        Cache::forget('settings');

        Notification::make()
            ->title('Cài đặt đã được lưu')
            ->success()
            ->send();
    }
}