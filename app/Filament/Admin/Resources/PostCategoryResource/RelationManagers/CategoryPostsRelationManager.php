<?php

namespace App\Filament\Admin\Resources\PostCategoryResource\RelationManagers;

use App\Models\Post;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables;
use Illuminate\Support\Str;

class CategoryPostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    protected static ?string $title = 'Bài viết';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin bài viết')
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
                            ->unique(Post::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Loại bài viết')
                            ->options([
                                'normal' => 'Bài viết thường',
                                'news' => 'Tin tức',
                                'service' => 'Dịch vụ',
                                'course' => 'Khóa học',
                            ])
                            ->default('normal')
                            ->required(),

                        FileUpload::make('thumbnail')
                            ->label('Hình đại diện')
                            ->image()
                            ->directory('posts/thumbnails')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(800)
                            ->imageResizeTargetHeight(600)
                            ->maxSize(3072)
                            ->imageEditor(),
                    ])->columns(2),

                Section::make('Nội dung bài viết')
                    ->description('🎨 Tạo nội dung linh hoạt với các khối text, ảnh, video. Kéo thả để sắp xếp lại thứ tự.')
                    ->schema([
                        Builder::make('content_builder')
                            ->label('Nội dung chi tiết')
                            ->blocks([
                                Builder\Block::make('paragraph')
                                    ->label('📝 Đoạn văn')
                                    ->icon('heroicon-m-document-text')
                                    ->schema([
                                        RichEditor::make('content')
                                            ->label('Nội dung')
                                            ->placeholder('Nhập nội dung đoạn văn...')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                                'blockquote',
                                                'codeBlock',
                                            ])
                                            ->required(),
                                    ]),

                                Builder\Block::make('image')
                                    ->label('🖼️ Hình ảnh')
                                    ->icon('heroicon-m-photo')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Hình ảnh')
                                            ->image()
                                            ->directory('posts/content')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth(1200)
                                            ->imageResizeTargetHeight(800)
                                            ->maxSize(5120)
                                            ->imageEditor()
                                            ->required(),

                                        TextInput::make('alt')
                                            ->label('Mô tả ảnh (Alt text)')
                                            ->placeholder('Mô tả ngắn gọn về hình ảnh...')
                                            ->maxLength(255),

                                        TextInput::make('caption')
                                            ->label('Chú thích')
                                            ->placeholder('Chú thích hiển thị dưới ảnh...')
                                            ->maxLength(500),
                                    ]),

                                Builder\Block::make('video')
                                    ->label('🎥 Video')
                                    ->icon('heroicon-m-play')
                                    ->schema([
                                        Select::make('video_type')
                                            ->label('Loại video')
                                            ->options([
                                                'youtube' => 'YouTube',
                                                'vimeo' => 'Vimeo',
                                                'upload' => 'Tải lên',
                                            ])
                                            ->default('youtube')
                                            ->live()
                                            ->required(),

                                        TextInput::make('video_url')
                                            ->label('URL Video')
                                            ->placeholder('https://www.youtube.com/watch?v=...')
                                            ->url()
                                            ->visible(fn (callable $get) => in_array($get('video_type'), ['youtube', 'vimeo']))
                                            ->required(fn (callable $get) => in_array($get('video_type'), ['youtube', 'vimeo'])),

                                        FileUpload::make('video_file')
                                            ->label('File Video')
                                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                                            ->directory('posts/videos')
                                            ->visibility('public')
                                            ->maxSize(51200) // 50MB
                                            ->visible(fn (callable $get) => $get('video_type') === 'upload')
                                            ->required(fn (callable $get) => $get('video_type') === 'upload'),

                                        TextInput::make('video_title')
                                            ->label('Tiêu đề video')
                                            ->placeholder('Tiêu đề mô tả video...')
                                            ->maxLength(255),
                                    ]),

                                Builder\Block::make('gallery')
                                    ->label('🖼️ Thư viện ảnh')
                                    ->icon('heroicon-m-photo')
                                    ->schema([
                                        FileUpload::make('images')
                                            ->label('Hình ảnh')
                                            ->image()
                                            ->multiple()
                                            ->directory('posts/galleries')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth(1200)
                                            ->imageResizeTargetHeight(800)
                                            ->maxSize(5120)
                                            ->maxFiles(10)
                                            ->imageEditor()
                                            ->reorderable()
                                            ->required(),

                                        TextInput::make('gallery_title')
                                            ->label('Tiêu đề thư viện')
                                            ->placeholder('Tiêu đề cho bộ sưu tập ảnh...')
                                            ->maxLength(255),
                                    ]),

                                Builder\Block::make('quote')
                                    ->label('💬 Trích dẫn')
                                    ->icon('heroicon-m-chat-bubble-left-right')
                                    ->schema([
                                        Textarea::make('quote')
                                            ->label('Nội dung trích dẫn')
                                            ->placeholder('Nhập nội dung trích dẫn...')
                                            ->rows(3)
                                            ->required(),

                                        TextInput::make('author')
                                            ->label('Tác giả')
                                            ->placeholder('Tên tác giả...')
                                            ->maxLength(255),

                                        TextInput::make('author_title')
                                            ->label('Chức danh tác giả')
                                            ->placeholder('Chức danh, công ty...')
                                            ->maxLength(255),
                                    ]),

                                Builder\Block::make('divider')
                                    ->label('➖ Đường phân cách')
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

                                        Select::make('width')
                                            ->label('Độ dày')
                                            ->options([
                                                'thin' => 'Mỏng',
                                                'medium' => 'Vừa',
                                                'thick' => 'Dày',
                                            ])
                                            ->default('medium'),
                                    ]),
                            ])
                            ->collapsible()
                            ->cloneable()
                            ->reorderable()
                            ->columnSpanFull()
                            ->minItems(1),
                    ]),

                Section::make('Cấu hình hiển thị')
                    ->schema([
                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0),

                        Toggle::make('is_featured')
                            ->label('Nổi bật')
                            ->default(false),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'active' => 'Hiển thị',
                                'inactive' => 'Ẩn',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Hình đại diện')
                    ->defaultImageUrl(fn() => asset('images/default-post.jpg'))
                    ->size(60)
                    ->extraImgAttributes(['class' => 'object-cover rounded-lg']),

                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'normal' => 'gray',
                        'news' => 'success',
                        'service' => 'warning',
                        'course' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'normal' => 'Bài viết',
                        'news' => 'Tin tức',
                        'service' => 'Dịch vụ',
                        'course' => 'Khóa học',
                        default => $state,
                    }),

                ToggleColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại bài viết')
                    ->options([
                        'normal' => 'Bài viết thường',
                        'news' => 'Tin tức',
                        'service' => 'Dịch vụ',
                        'course' => 'Khóa học',
                    ]),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Nổi bật'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm bài viết')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Tự động tạo content từ content_builder
                        if (!empty($data['content_builder'])) {
                            $data['content'] = $this->extractContentFromBuilder($data['content_builder']);
                        } else {
                            $data['content'] = '';
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Tự động tạo content từ content_builder
                        if (!empty($data['content_builder'])) {
                            $data['content'] = $this->extractContentFromBuilder($data['content_builder']);
                        } else {
                            $data['content'] = '';
                        }
                        return $data;
                    }),
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
                    if (!empty($block['data']['content'])) {
                        $content .= strip_tags($block['data']['content']) . "\n\n";
                    }
                    break;

                case 'quote':
                    if (!empty($block['data']['quote'])) {
                        $content .= '"' . $block['data']['quote'] . '"';
                        if (!empty($block['data']['author'])) {
                            $content .= ' - ' . $block['data']['author'];
                        }
                        $content .= "\n\n";
                    }
                    break;

                case 'image':
                    if (!empty($block['data']['caption'])) {
                        $content .= $block['data']['caption'] . "\n\n";
                    }
                    break;

                case 'video':
                    if (!empty($block['data']['video_title'])) {
                        $content .= $block['data']['video_title'] . "\n\n";
                    }
                    break;

                case 'gallery':
                    if (!empty($block['data']['gallery_title'])) {
                        $content .= $block['data']['gallery_title'] . "\n\n";
                    }
                    break;
            }
        }

        return trim($content);
    }
}
