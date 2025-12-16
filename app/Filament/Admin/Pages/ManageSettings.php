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
                        TextInput::make('slogan')
                            ->label('Slogan')
                            ->maxLength(255),
                        TextInput::make('hotline')
                            ->label('Hotline')
                            ->required()
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label('Địa chỉ')
                            ->rows(2)
                            ->maxLength(500),
                        TextInput::make('working_hours')
                            ->label('Giờ làm việc')
                            ->maxLength(255),
                        FileUpload::make('logo_link')
                            ->label('Logo')
                            ->helperText('Tỷ lệ 2:1 (400x200px)')
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
                                return $imageService->saveImageWithAspectRatio($file, 'settings/logos', 400, 200, 100, "logo-{$siteName}");
                            }),
                        FileUpload::make('favicon_link')
                            ->label('Favicon')
                            ->helperText('32x32px')
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
                                return $imageService->saveImageWithAspectRatio($file, 'settings/favicons', 32, 32, 100, "favicon-{$siteName}");
                            }),
                    ])
                    ->columns(2),

                Section::make('Mạng xã hội & SEO')
                    ->schema([
                        TextInput::make('facebook_link')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('zalo_link')
                            ->label('Zalo')
                            ->maxLength(255),
                        TextInput::make('youtube_link')
                            ->label('YouTube')
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
                        TextInput::make('seo_title')
                            ->label('Tiêu đề SEO')
                            ->maxLength(255),
                        Textarea::make('seo_description')
                            ->label('Mô tả SEO')
                            ->rows(2)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        FileUpload::make('og_image_link')
                            ->label('Ảnh OG (Social)')
                            ->helperText('1200x630px')
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
                                return $imageService->saveImageWithAspectRatio($file, 'settings/og-images', 1200, 630, 85, "og-image-{$siteName}");
                            }),
                        FileUpload::make('placeholder_image')
                            ->label('Ảnh Placeholder')
                            ->helperText('Ảnh mặc định khi không có ảnh')
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
                                return $imageService->saveImageWithAspectRatio($file, 'settings/placeholders', 400, 400, 90, "placeholder-{$siteName}");
                            }),
                    ])
                    ->columns(2)
                    ->collapsible(),
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