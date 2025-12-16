<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\RelationManagers;
use App\Models\Post;
use App\Services\ImageService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use FilamentTiptapEditor\TiptapEditor;
use FilamentTiptapEditor\Enums\TiptapOutput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Models\CatPost;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

use Illuminate\Support\Str;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class PostResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Post::class;

    protected static ?string $modelLabel = 'bài viết';

    protected static ?string $pluralModelLabel = 'bài viết';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = NavigationGroups::CONTENT_MANAGEMENT;

    protected static ?string $navigationLabel = 'Bài viết';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Thông tin chính')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Section::make('Thông tin cơ bản')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Tiêu đề')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),

                                        TextInput::make('slug')
                                            ->label('Đường dẫn')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->suffixAction(
                                                Action::make('generateSlug')
                                                    ->icon('heroicon-m-link')
                                                    ->tooltip('Tự động tạo từ tiêu đề')
                                                    ->action(function (Set $set, Get $get) {
                                                        $title = $get('title');
                                                        if (!empty($title)) {
                                                            $set('slug', Str::slug($title));
                                                        }
                                                    })
                                            ),

                                        Select::make('categories')
                                            ->label('Chuyên mục')
                                            ->multiple()
                                            ->relationship(
                                                'categories',
                                                'name',
                                                fn (EloquentBuilder $query) => $query
                                                    ->where('status', 'active')
                                                    ->orderBy('order')
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Tên chuyên mục')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                                                TextInput::make('slug')
                                                    ->label('Đường dẫn')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('description')
                                                    ->label('Mô tả')
                                                    ->maxLength(500),
                                                Select::make('status')
                                                    ->label('Trạng thái')
                                                    ->options([
                                                        'active' => 'Hiển thị',
                                                        'inactive' => 'Ẩn',
                                                    ])
                                                    ->default('active')
                                                    ->required(),
                                            ])
                                            ->createOptionUsing(function (array $data) {
                                                $data['order'] = CatPost::max('order') + 1;
                                                return CatPost::create($data)->getKey();
                                            }),

                                    ])->columns(2),

                                Section::make('Hình ảnh và cấu hình')
                                    ->schema([
                                        FileUpload::make('thumbnail')
                                            ->label('Hình đại diện')
                                            ->helperText('Kích thước khuyến nghị: 1200x630px (tỷ lệ 1.91:1) cho hiển thị tối ưu trên mạng xã hội')
                                            ->image()
                                            ->directory('posts/thumbnails')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->imageEditor()
                                            ->imagePreviewHeight('200')
                                            ->nullable()
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->saveUploadedFileUsing(function ($file, $get) {
                                                $imageService = app(ImageService::class);
                                                $title = $get('title') ?? 'bai-viet';
                                                return $imageService->saveImageWithAspectRatio(
                                                    $file,
                                                    'posts/thumbnails',
                                                    1200,
                                                    630,
                                                    90,
                                                    "thumbnail-{$title}"
                                                );
                                            })
                                            ->loadStateFromRelationshipsUsing(function ($state) {
                                                // Force Filament to recognize existing image
                                                if ($state && is_string($state)) {
                                                    return [$state];
                                                }
                                                return $state;
                                            })
                                            ->downloadable()
                                            ->openable()
                                            ->columnSpan(2),

                                        // Select::make('display_thumbnail')
                                        //     ->label('Hiển thị ảnh đại diện')
                                        //     ->helperText('Chọn hiển thị hoặc ẩn ảnh đại diện ở trang chi tiết bài viết')
                                        //     ->options([
                                        //         1 => '✅ Hiển thị ảnh đại diện',
                                        //         0 => '❌ Ẩn ảnh đại diện',
                                        //     ])
                                        //     ->default(1)
                                        //     ->selectablePlaceholder(false)
                                        //     ->native(false)
                                        //     ->afterStateHydrated(function (Select $component, $state) {
                                        //         $component->state((int) $state);
                                        //     })
                                        //     ->dehydrateStateUsing(fn ($state): bool => (bool) $state),


                                        Toggle::make('display_thumbnail')
                                            ->label('Hiển thị ảnh đại diện')
                                            ->helperText('Bật/tắt hiển thị ảnh đại diện ở trang chi tiết bài viết')
                                            ->default(true),

                                        Toggle::make('is_featured')
                                            ->label('Nổi bật')
                                            ->default(false)
                                            ->onColor('success')
                                            ->offColor('danger'),

                                        Select::make('status')
                                            ->label('Trạng thái')
                                            ->options([
                                                'active' => 'Hiển thị',
                                                'inactive' => 'Ẩn',
                                            ])
                                            ->default('active')
                                            ->required(),

                                        TextInput::make('order')
                                            ->label('Thứ tự hiển thị')
                                            ->integer()
                                            ->default(0),
                                    ])->columns(3),

                                Section::make('Nội dung bài viết')
                                    ->description('Tạo nội dung linh hoạt với các khối text, ảnh, video. Kéo thả để sắp xếp lại thứ tự.')
                                    ->schema([
                                        Builder::make('content_builder')
                                            ->label('Nội dung chi tiết')
                                            ->blocks([
                                                Builder\Block::make('paragraph')
                                                    ->label('Đoạn văn')
                                                    ->icon('heroicon-m-document-text')
                                                    ->schema([
                                                        TiptapEditor::make('content')
                                                            ->label('Nội dung')
                                                            ->placeholder('Nhập nội dung đoạn văn...')
                                                            ->profile('rich')
                                                            ->output(TiptapOutput::Html)
                                                            ->maxContentWidth('full')
                                                            ->disk('public')
                                                            ->directory('posts/content')
                                                            ->required(),
                                                    ]),

                                                Builder\Block::make('image')
                                                    ->label('Hình ảnh')
                                                    ->icon('heroicon-m-photo')
                                                    ->schema([
                                                        FileUpload::make('image')
                                                            ->label('Chọn ảnh')
                                                            ->image()
                                                            ->directory('posts/content')
                                                            ->visibility('public')
                                                            ->imageEditor()
                                                            ->imagePreviewHeight('200')
                                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                                                            ->saveUploadedFileUsing(function ($file, $get) {
                                                                $imageService = app(\App\Services\ImageService::class);
                                                                $title = $get('../../../title') ?? 'bai-viet';
                                                                return $imageService->saveRichEditorFile(
                                                                    $file,
                                                                    'posts/content',
                                                                    1200,
                                                                    85,
                                                                    "content-{$title}-" . time()
                                                                );
                                                            })
                                                            ->required(),

                                                        TextInput::make('alt')
                                                            ->label('Mô tả ảnh (Alt text)')
                                                            ->placeholder('Mô tả ngắn gọn về ảnh...')
                                                            ->helperText('Quan trọng cho SEO và accessibility'),

                                                        TextInput::make('caption')
                                                            ->label('Chú thích ảnh')
                                                            ->placeholder('Chú thích hiển thị dưới ảnh...'),
                                                    ]),

                                                Builder\Block::make('gallery')
                                                    ->label('Thư viện ảnh')
                                                    ->icon('heroicon-m-photo')
                                                    ->schema([
                                                        FileUpload::make('images')
                                                            ->label('Chọn nhiều ảnh')
                                                            ->image()
                                                            ->multiple()
                                                            ->directory('posts/galleries')
                                                            ->visibility('public')
                                                            ->imageEditor()
                                                            ->imagePreviewHeight('150')
                                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                                                            ->saveUploadedFileUsing(function ($file, $get) {
                                                                $imageService = app(\App\Services\ImageService::class);
                                                                $title = $get('../../../title') ?? 'bai-viet';
                                                                return $imageService->saveRichEditorFile(
                                                                    $file,
                                                                    'posts/galleries',
                                                                    1200,
                                                                    85,
                                                                    "gallery-{$title}-" . time()
                                                                );
                                                            })
                                                            ->required(),

                                                        TextInput::make('caption')
                                                            ->label('Chú thích thư viện')
                                                            ->placeholder('Mô tả về bộ sưu tập ảnh...'),
                                                    ]),

                                                Builder\Block::make('quote')
                                                    ->label('Trích dẫn')
                                                    ->icon('heroicon-m-chat-bubble-left-right')
                                                    ->schema([
                                                        Textarea::make('content')
                                                            ->label('Nội dung trích dẫn')
                                                            ->placeholder('Nhập nội dung trích dẫn...')
                                                            ->rows(3)
                                                            ->required(),

                                                        TextInput::make('author')
                                                            ->label('Tác giả')
                                                            ->placeholder('Tên tác giả...'),

                                                        TextInput::make('source')
                                                            ->label('Nguồn')
                                                            ->placeholder('Nguồn trích dẫn...'),
                                                    ]),

                                                Builder\Block::make('video')
                                                    ->label('Video')
                                                    ->icon('heroicon-m-play')
                                                    ->schema([
                                                        Select::make('type')
                                                            ->label('Loại video')
                                                            ->options([
                                                                'youtube' => 'YouTube',
                                                                'vimeo' => 'Vimeo',
                                                                'upload' => 'Upload file',
                                                            ])
                                                            ->default('youtube')
                                                            ->live()
                                                            ->required(),

                                                        TextInput::make('url')
                                                            ->label('URL Video')
                                                            ->placeholder('https://www.youtube.com/watch?v=...')
                                                            ->url()
                                                            ->visible(fn (Get $get) => in_array($get('type'), ['youtube', 'vimeo']))
                                                            ->required(fn (Get $get) => in_array($get('type'), ['youtube', 'vimeo'])),

                                                        FileUpload::make('file')
                                                            ->label('Upload video')
                                                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                                                            ->directory('posts/videos')
                                                            ->visibility('public')
                                                            ->visible(fn (Get $get) => $get('type') === 'upload')
                                                            ->required(fn (Get $get) => $get('type') === 'upload'),

                                                        TextInput::make('caption')
                                                            ->label('Chú thích video')
                                                            ->placeholder('Mô tả về video...'),
                                                    ]),

                                                Builder\Block::make('divider')
                                                    ->label('Đường phân cách')
                                                    ->icon('heroicon-m-minus')
                                                    ->schema([
                                                        Select::make('style')
                                                            ->label('Kiểu đường kẻ')
                                                            ->options([
                                                                'solid' => 'Liền',
                                                                'dashed' => 'Gạch ngang',
                                                                'dotted' => 'Chấm',
                                                                'double' => 'Đôi',
                                                            ])
                                                            ->default('solid'),
                                                    ]),

                                                Builder\Block::make('google_map')
                                                    ->label('Nhúng Google Maps')
                                                    ->icon('heroicon-m-map')
                                                    ->schema([
                                                        Textarea::make('embed_code')
                                                            ->label('Mã nhúng Google Maps')
                                                            ->placeholder('Dán mã iframe từ Google Maps...')
                                                            ->helperText('Vào Google Maps → Chia sẻ → Nhúng bản đồ → Sao chép mã HTML')
                                                            ->rows(4)
                                                            ->required(),

                                                        TextInput::make('title')
                                                            ->label('Tiêu đề bản đồ')
                                                            ->placeholder('Ví dụ: Địa chỉ cửa hàng của chúng tôi'),

                                                        Select::make('height')
                                                            ->label('Chiều cao bản đồ')
                                                            ->options([
                                                                '300' => 'Thấp (300px)',
                                                                '400' => 'Trung bình (400px)',
                                                                '500' => 'Cao (500px)',
                                                                '600' => 'Rất cao (600px)',
                                                            ])
                                                            ->default('400'),
                                                    ]),
                                            ])
                                            ->blockNumbers(false)
                                            ->addActionLabel('➕ Thêm khối nội dung')
                                            ->collapsible()
                                            ->cloneable()
                                            ->reorderable()
                                            ->deleteAction(
                                                fn (Action $action) => $action->requiresConfirmation()
                                            )
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('SEO & Cấu hình')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Section::make('SEO và Thông tin khác')
                                    ->description('Các thông tin SEO sẽ được tự động tạo khi lưu nếu để trống. Bạn cũng có thể sử dụng nút bên dưới để tạo thủ công.')
                                    ->schema([
                                        Actions::make([
                                            Action::make('generateAllSeo')
                                                ->label('🚀 Tự động tạo SEO')
                                                ->icon('heroicon-m-sparkles')
                                                ->color('success')
                                                ->size('lg')
                                                ->action(function (Set $set, Get $get) {
                                                    $title = $get('title');
                                                    $content = $get('content');

                                                    $messages = [];

                                                    // Tạo SEO title
                                                    if (!empty($title)) {
                                                        $seoTitle = static::generateSeoTitle($title);
                                                        $set('seo_title', $seoTitle);
                                                        $messages[] = 'SEO title';
                                                    }

                                                    // Tạo SEO description
                                                    if (!empty($content)) {
                                                        $seoDescription = static::generateSeoDescription($content);
                                                        $set('seo_description', $seoDescription);
                                                        $messages[] = 'SEO description';
                                                    }

                                                    // Thông báo kết quả
                                                    if (empty($messages)) {
                                                        \Filament\Notifications\Notification::make()
                                                            ->title('Chưa thể tạo SEO')
                                                            ->body('Vui lòng nhập tiêu đề và nội dung trước khi tạo SEO.')
                                                            ->warning()
                                                            ->send();
                                                    } else {
                                                        \Filament\Notifications\Notification::make()
                                                            ->title('Đã tạo SEO thành công!')
                                                            ->body('Đã tạo: ' . implode(', ', $messages) . '. OG image sẽ tự động copy từ hình đại diện khi lưu.')
                                                            ->success()
                                                            ->send();
                                                    }
                                                })
                                        ])->columnSpanFull(),

                                        TextInput::make('seo_title')
                                            ->label('Tiêu đề SEO')
                                            ->helperText('Tối đa 60 ký tự cho SEO tốt nhất. Để trống sẽ tự động tạo từ tiêu đề.')
                                            ->maxLength(255),

                                        Textarea::make('seo_description')
                                            ->label('Mô tả SEO')
                                            ->helperText('Tối đa 155 ký tự cho SEO tốt nhất. Để trống sẽ tự động tạo từ nội dung.')
                                            ->rows(3)
                                            ->maxLength(255),

                                        FileUpload::make('og_image_link')
                                            ->label('Hình ảnh OG (Social Media)')
                                            ->helperText('Kích thước tối ưu: 1200x630px. Để trống sẽ tự động copy từ hình đại diện.')
                                            ->image()
                                            ->directory('posts/og-images')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth(1200)
                                            ->imageResizeTargetHeight(630)
                                            ->saveUploadedFileUsing(function ($file, $get) {
                                                $imageService = app(\App\Services\ImageService::class);
                                                $title = $get('title') ?? 'bai-viet';
                                                return $imageService->saveImage(
                                                    $file,
                                                    'posts/og-images',
                                                    1200,
                                                    630,
                                                    85,
                                                    "og-image-{$title}"
                                                );
                                            }),
                                    ])->columns(2),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail') // Giữ tên trường là 'thumbnail' để khớp với database
                    ->label('Hình đại diện')
                    ->defaultImageUrl(fn() => asset('images/default-post.jpg'))
                    ->size(80) // Kích thước cố định
                    ->extraImgAttributes(['class' => 'object-cover rounded-lg']) // Không bị méo, bo góc
                    ->tooltip(fn ($record) => $record->title), // Hiển thị tiêu đề khi hover

                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->wrap()
                    ->width('200px'),

                TextColumn::make('categories.name')
                    ->label('Chuyên mục')
                    ->badge()
                    ->separator(',')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                    })
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                TextColumn::make('updater.name')
                    ->label('Người chỉnh sửa')
                    ->default('Chưa cập nhật')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->updated_at ? 'Cập nhật lúc: ' . $record->updated_at->format('d/m/Y H:i') : 'Chưa có thông tin'),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->reorderable('order')
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->label('Chuyên mục'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Nổi bật'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }

    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()
            ->with(['categories:id,name', 'creator:id,name', 'updater:id,name'])
            ->select(['id', 'title', 'slug', 'content', 'content_builder', 'seo_title', 'seo_description', 'og_image_link', 'thumbnail', 'display_thumbnail', 'status', 'is_featured', 'order', 'created_by', 'updated_by', 'created_at', 'updated_at']);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PostImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    /**
     * Tạo SEO title từ title gốc
     */
    public static function generateSeoTitle(string $title): string
    {
        // Giới hạn độ dài SEO title trong khoảng 50-60 ký tự
        $maxLength = 60;

        if (strlen($title) <= $maxLength) {
            return $title;
        }

        // Cắt ngắn tại từ cuối cùng để tránh cắt giữa từ
        $truncated = substr($title, 0, $maxLength - 3);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }

    /**
     * Tạo SEO description từ content
     */
    public static function generateSeoDescription(string $content): string
    {
        // Loại bỏ HTML tags
        $plainText = strip_tags($content);

        // Loại bỏ khoảng trắng thừa
        $plainText = preg_replace('/\s+/', ' ', trim($plainText));

        // Giới hạn độ dài SEO description trong khoảng 150-160 ký tự
        $maxLength = 155;

        if (strlen($plainText) <= $maxLength) {
            return $plainText;
        }

        // Cắt ngắn tại từ cuối cùng để tránh cắt giữa từ
        $truncated = substr($plainText, 0, $maxLength - 3);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }
}