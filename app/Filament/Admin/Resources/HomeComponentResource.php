<?php

namespace App\Filament\Admin\Resources;

use App\Enums\HomeComponentType;
use App\Filament\Admin\Resources\HomeComponentResource\Pages;
use App\Models\HomeComponent;
use App\Models\Post;
use App\Models\Product;
use App\Models\CatProduct;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class HomeComponentResource extends Resource
{
    use HasRoleBasedAccess;

    protected static ?string $model = HomeComponent::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = NavigationGroups::WEBSITE_SETTINGS;

    protected static ?string $modelLabel = 'Section trang chủ';

    protected static ?string $pluralModelLabel = 'Quản lý trang chủ';

    protected static ?string $navigationLabel = 'Trang chủ';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cấu hình chung')
                    ->schema([
                        Select::make('type')
                            ->label('Loại Section')
                            ->options(HomeComponentType::options())
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('config', []))
                            ->disabled(fn (?HomeComponent $record) => $record !== null)
                            ->helperText(fn (?HomeComponent $record) => $record
                                ? 'Không thể thay đổi loại sau khi tạo'
                                : 'Chọn loại section bạn muốn thêm')
                            ->columnSpan(2),

                        Toggle::make('active')
                            ->label('Hiển thị')
                            ->default(true)
                            ->helperText('Bật/tắt hiển thị section này'),

                        TextInput::make('order')
                            ->label('Thứ tự')
                            ->numeric()
                            ->default(0)
                            ->helperText('Số nhỏ hơn hiển thị trước'),
                    ])
                    ->columns(4),

                Section::make('Nội dung')
                    ->schema(fn (Get $get) => static::getConfigFields($get('type')))
                    ->visible(fn (Get $get) => $get('type') !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                Tables\Columns\IconColumn::make('type')
                    ->label('Icon')
                    ->icon(fn (HomeComponent $record): string =>
                        HomeComponentType::tryFrom($record->type)?->getIcon() ?? 'heroicon-o-question-mark-circle'
                    )
                    ->width(60),

                Tables\Columns\TextColumn::make('type')
                    ->label('Loại')
                    ->formatStateUsing(fn (string $state): string =>
                        HomeComponentType::tryFrom($state)?->getLabel() ?? $state
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('config_summary')
                    ->label('Tóm tắt')
                    ->getStateUsing(fn (HomeComponent $record): string =>
                        static::getConfigSummary($record)
                    )
                    ->wrap()
                    ->limit(80),

                Tables\Columns\ToggleColumn::make('active')
                    ->label('Hiển thị'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại')
                    ->options(HomeComponentType::options()),

                Tables\Filters\TernaryFilter::make('active')
                    ->label('Trạng thái')
                    ->trueLabel('Đang hiển thị')
                    ->falseLabel('Đang ẩn'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa'),
                Tables\Actions\DeleteAction::make()->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Xóa đã chọn'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeComponents::route('/'),
            'create' => Pages\CreateHomeComponent::route('/create'),
            'edit' => Pages\EditHomeComponent::route('/{record}/edit'),
        ];
    }

    public static function getConfigFields(?string $type): array
    {
        if (!$type) {
            return [];
        }

        return match ($type) {
            HomeComponentType::HeroCarousel->value => static::heroCarouselFields(),
            HomeComponentType::Stats->value => static::statsFields(),
            HomeComponentType::About->value => static::aboutFields(),
            HomeComponentType::ProductCategories->value => static::productCategoriesFields(),
            HomeComponentType::FeaturedProducts->value => static::featuredProductsFields(),
            HomeComponentType::Slogan->value => static::sloganFields(),
            HomeComponentType::Partners->value => static::partnersFields(),
            HomeComponentType::News->value => static::newsFields(),
            HomeComponentType::Footer->value => static::footerFields(),
            default => [],
        };
    }

    public static function getConfigSummary(HomeComponent $record): string
    {
        $config = $record->config ?? [];

        return match ($record->type) {
            HomeComponentType::HeroCarousel->value =>
                count($config['slides'] ?? []) . ' slides',
            HomeComponentType::Stats->value =>
                count($config['items'] ?? []) . ' chỉ số',
            HomeComponentType::About->value =>
                $config['title'] ?? 'Chưa có tiêu đề',
            HomeComponentType::ProductCategories->value =>
                count($config['categories'] ?? []) . ' danh mục',
            HomeComponentType::FeaturedProducts->value =>
                count($config['products'] ?? []) . ' sản phẩm',
            HomeComponentType::Slogan->value =>
                $config['title'] ?? 'Chưa có tiêu đề',
            HomeComponentType::Partners->value =>
                count($config['partners'] ?? []) . ' đối tác',
            HomeComponentType::News->value =>
                ($config['display_mode'] ?? 'latest') === 'latest'
                    ? 'Hiển thị ' . ($config['limit'] ?? 6) . ' bài mới nhất'
                    : count($config['posts'] ?? []) . ' bài được chọn',
            HomeComponentType::Footer->value =>
                $config['company_name'] ?? 'Footer',
            default => 'N/A',
        };
    }

    protected static function heroCarouselFields(): array
    {
        return [
            Repeater::make('config.slides')
                ->label('Danh sách Slides')
                ->schema([
                    FileUpload::make('image')
                        ->label('Ảnh Banner')
                        ->image()
                        ->directory('banners')
                        ->disk('public')
                        ->required(),
                ])
                ->reorderable()
                ->collapsible()
                ->defaultItems(1)
                ->maxItems(10)
                ->itemLabel(fn (array $state): ?string => 'Slide'),
        ];
    }

    protected static function statsFields(): array
    {
        return [
            Repeater::make('config.items')
                ->label('Các chỉ số thống kê')
                ->schema([
                    TextInput::make('value')
                        ->label('Giá trị')
                        ->placeholder('1,500+')
                        ->required()
                        ->maxLength(20),

                    TextInput::make('label')
                        ->label('Mô tả')
                        ->placeholder('Khách hàng tin dùng')
                        ->required()
                        ->maxLength(100),
                ])
                ->columns(2)
                ->reorderable()
                ->defaultItems(4)
                ->maxItems(8),
        ];
    }

    protected static function aboutFields(): array
    {
        return [
            Section::make('Nội dung chính')
                ->schema([
                    TextInput::make('config.badge')
                        ->label('Badge text')
                        ->placeholder('VỀ CHÚNG TÔI')
                        ->maxLength(50),

                    TextInput::make('config.title')
                        ->label('Tiêu đề chính')
                        ->placeholder('Chào mừng đến với ESAT')
                        ->required()
                        ->maxLength(100),

                    Textarea::make('config.description')
                        ->label('Mô tả chi tiết')
                        ->rows(4)
                        ->maxLength(1000),

                    Textarea::make('config.quote')
                        ->label('Trích dẫn (Quote)')
                        ->placeholder('Cam kết mang đến giải pháp tối ưu...')
                        ->rows(2)
                        ->maxLength(500),

                    TextInput::make('config.button_text')
                        ->label('Text nút bấm')
                        ->placeholder('Tìm hiểu thêm về chúng tôi')
                        ->maxLength(100),

                    TextInput::make('config.button_url')
                        ->label('URL nút bấm')
                        ->placeholder('/gioi-thieu')
                        ->maxLength(255),
                ])
                ->columns(2),

            Section::make('4 Điểm nổi bật (cố định)')
                ->description('Chỉnh sửa 4 điểm nổi bật hiển thị bên phải')
                ->schema([
                    Section::make('Điểm nổi bật 1')
                        ->schema([
                            FileUpload::make('config.feature_1_image')
                                ->label('Hình ảnh')
                                ->image()
                                ->directory('about')
                                ->disk('public'),
                            TextInput::make('config.feature_1_title')
                                ->label('Tiêu đề')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('config.feature_1_desc')
                                ->label('Mô tả')
                                ->maxLength(300),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->collapsed(false),

                    Section::make('Điểm nổi bật 2')
                        ->schema([
                            FileUpload::make('config.feature_2_image')
                                ->label('Hình ảnh')
                                ->image()
                                ->directory('about')
                                ->disk('public'),
                            TextInput::make('config.feature_2_title')
                                ->label('Tiêu đề')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('config.feature_2_desc')
                                ->label('Mô tả')
                                ->maxLength(300),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->collapsed(true),

                    Section::make('Điểm nổi bật 3')
                        ->schema([
                            FileUpload::make('config.feature_3_image')
                                ->label('Hình ảnh')
                                ->image()
                                ->directory('about')
                                ->disk('public'),
                            TextInput::make('config.feature_3_title')
                                ->label('Tiêu đề')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('config.feature_3_desc')
                                ->label('Mô tả')
                                ->maxLength(300),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->collapsed(true),

                    Section::make('Điểm nổi bật 4')
                        ->schema([
                            FileUpload::make('config.feature_4_image')
                                ->label('Hình ảnh')
                                ->image()
                                ->directory('about')
                                ->disk('public'),
                            TextInput::make('config.feature_4_title')
                                ->label('Tiêu đề')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('config.feature_4_desc')
                                ->label('Mô tả')
                                ->maxLength(300),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->collapsed(true),
                ]),
        ];
    }

    protected static function productCategoriesFields(): array
    {
        return [
            TextInput::make('config.title')
                ->label('Tiêu đề section')
                ->default('Danh mục sản phẩm')
                ->maxLength(100),

            Repeater::make('config.categories')
                ->label('Danh sách danh mục')
                ->schema([
                    FileUpload::make('image')
                        ->label('Ảnh đại diện')
                        ->image()
                        ->directory('categories')
                        ->disk('public')
                        ->required(),

                    TextInput::make('name')
                        ->label('Tên danh mục')
                        ->required()
                        ->maxLength(50),

                    Select::make('link_type')
                        ->label('Loại liên kết')
                        ->options([
                            'category' => 'Chọn danh mục có sẵn',
                            'custom' => 'Tự nhập link',
                        ])
                        ->default('category')
                        ->live()
                        ->required(),

                    Select::make('category_id')
                        ->label('Chọn danh mục')
                        ->options(fn () => CatProduct::query()
                            ->where('status', 'active')
                            ->orderBy('order')
                            ->pluck('name', 'id')
                            ->toArray()
                        )
                        ->searchable()
                        ->visible(fn (Get $get) => $get('link_type') === 'category')
                        ->required(fn (Get $get) => $get('link_type') === 'category')
                        ->reactive()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state) {
                                $category = CatProduct::find($state);
                                if ($category) {
                                    $set('link', '/san-pham?category=' . $category->slug);
                                }
                            }
                        }),

                    TextInput::make('link')
                        ->label('Link tùy chỉnh')
                        ->placeholder('/san-pham?category=custom-slug')
                        ->visible(fn (Get $get) => $get('link_type') === 'custom')
                        ->required(fn (Get $get) => $get('link_type') === 'custom'),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible()
                ->maxItems(12)
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Danh mục'),
        ];
    }

    protected static function featuredProductsFields(): array
    {
        return [
            TextInput::make('config.title')
                ->label('Tiêu đề section')
                ->default('Sản phẩm nổi bật')
                ->maxLength(100),

            TextInput::make('config.subtitle')
                ->label('Mô tả section')
                ->maxLength(200),

            Select::make('config.display_mode')
                ->label('Chế độ hiển thị')
                ->options([
                    'manual' => 'Chọn thủ công',
                    'latest' => 'Sản phẩm mới nhất',
                    'featured' => 'Sản phẩm nổi bật (is_featured)',
                ])
                ->default('featured')
                ->live(),

            TextInput::make('config.limit')
                ->label('Số lượng hiển thị')
                ->numeric()
                ->default(8)
                ->minValue(4)
                ->maxValue(24)
                ->visible(fn (Get $get) => $get('config.display_mode') !== 'manual'),

            Repeater::make('config.products')
                ->label('Chọn sản phẩm')
                ->visible(fn (Get $get) => $get('config.display_mode') === 'manual')
                ->schema([
                    Select::make('product_id')
                        ->label('Chọn sản phẩm')
                        ->options(fn () => Product::where('status', 'active')
                            ->orderBy('created_at', 'desc')
                            ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state) {
                                $product = Product::find($state);
                                if ($product) {
                                    $set('name', $product->name);
                                    $set('link', '/san-pham/' . $product->slug);
                                    $set('image', $product->thumbnail);
                                    $set('price', $product->price ? number_format($product->price, 0, ',', '.') . ' đ' : 'Liên hệ');
                                }
                            }
                        }),

                    TextInput::make('name')
                        ->label('Tên sản phẩm')
                        ->disabled(),

                    TextInput::make('price')
                        ->label('Giá')
                        ->disabled(),
                ])
                ->columns(3)
                ->reorderable()
                ->maxItems(24)
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Sản phẩm'),

            TextInput::make('config.view_all_link')
                ->label('Link "Xem tất cả"')
                ->placeholder('/san-pham')
                ->default('/san-pham'),
        ];
    }

    protected static function sloganFields(): array
    {
        return [
            TextInput::make('config.title')
                ->label('Tiêu đề chính')
                ->placeholder('Nhà Phân Phối Thiết Bị Công Nghệ')
                ->required()
                ->maxLength(200),

            TextInput::make('config.subtitle')
                ->label('Tiêu đề phụ')
                ->placeholder('VÌ GIẢI PHÁP KẾT NỐI THÔNG MINH')
                ->maxLength(200),
        ];
    }

    protected static function partnersFields(): array
    {
        return [
            TextInput::make('config.title')
                ->label('Tiêu đề section')
                ->default('Đối tác')
                ->maxLength(100),

            TextInput::make('config.subtitle')
                ->label('Tiêu đề phụ')
                ->maxLength(200),

            Select::make('config.display_mode')
                ->label('Chế độ hiển thị')
                ->options([
                    'auto' => 'Tự động từ Partner model',
                    'manual' => 'Chọn thủ công',
                ])
                ->default('auto')
                ->live(),

            TextInput::make('config.limit')
                ->label('Số lượng hiển thị')
                ->numeric()
                ->default(10)
                ->visible(fn (Get $get) => $get('config.display_mode') === 'auto'),

            Repeater::make('config.partners')
                ->label('Danh sách đối tác')
                ->visible(fn (Get $get) => $get('config.display_mode') === 'manual')
                ->schema([
                    FileUpload::make('logo')
                        ->label('Logo đối tác')
                        ->image()
                        ->directory('partners')
                        ->disk('public')
                        ->required(),

                    TextInput::make('name')
                        ->label('Tên đối tác')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('link')
                        ->label('Website')
                        ->placeholder('https://partner.com'),
                ])
                ->columns(3)
                ->reorderable()
                ->collapsible()
                ->maxItems(20)
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Đối tác'),

            Toggle::make('config.auto_scroll')
                ->label('Tự động cuộn')
                ->default(true),
        ];
    }

    protected static function newsFields(): array
    {
        return [
            TextInput::make('config.title')
                ->label('Tiêu đề section')
                ->default('Tin tức')
                ->maxLength(100),

            TextInput::make('config.subtitle')
                ->label('Tiêu đề phụ')
                ->maxLength(200),

            Select::make('config.display_mode')
                ->label('Chế độ hiển thị')
                ->options([
                    'latest' => 'Bài mới nhất',
                    'manual' => 'Chọn thủ công',
                ])
                ->default('latest')
                ->live(),

            TextInput::make('config.limit')
                ->label('Số bài hiển thị')
                ->numeric()
                ->default(6)
                ->minValue(3)
                ->maxValue(12)
                ->visible(fn (Get $get) => $get('config.display_mode') === 'latest'),

            Repeater::make('config.posts')
                ->label('Chọn bài viết')
                ->visible(fn (Get $get) => $get('config.display_mode') === 'manual')
                ->schema([
                    Select::make('post_id')
                        ->label('Chọn bài viết')
                        ->options(fn () => Post::where('status', 'active')
                            ->orderBy('created_at', 'desc')
                            ->pluck('title', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state) {
                                $post = Post::find($state);
                                if ($post) {
                                    $set('title', $post->title);
                                    $set('link', '/bai-viet/' . $post->slug);
                                    $set('image', $post->thumbnail);
                                }
                            }
                        }),

                    TextInput::make('title')
                        ->label('Tiêu đề')
                        ->disabled(),
                ])
                ->columns(2)
                ->reorderable()
                ->maxItems(12)
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Bài viết'),

            TextInput::make('config.view_all_link')
                ->label('Link "Xem tất cả"')
                ->placeholder('/bai-viet')
                ->default('/bai-viet'),
        ];
    }

    protected static function footerFields(): array
    {
        return [
            Section::make('CTA - Call to Action (phía trên footer)')
                ->schema([
                    TextInput::make('config.cta_badge')
                        ->label('Badge')
                        ->placeholder('GIẢI PHÁP CÔNG NGHỆ')
                        ->maxLength(50),

                    TextInput::make('config.cta_title')
                        ->label('Tiêu đề')
                        ->placeholder('Cùng kết nối công nghệ nâng tầm hiệu quả')
                        ->maxLength(200),

                    TextInput::make('config.cta_button_text')
                        ->label('Text nút')
                        ->placeholder('Tìm hiểu ngay')
                        ->maxLength(50),

                    TextInput::make('config.cta_button_url')
                        ->label('URL nút')
                        ->placeholder('/lien-he')
                        ->maxLength(255),
                ])
                ->columns(2)
                ->collapsible()
                ->collapsed(),

            Section::make('Cột 1 - Thông tin công ty')
                ->schema([
                    TextInput::make('config.company_name')
                        ->label('Tên công ty')
                        ->placeholder('CÔNG TY TNHH ESAT')
                        ->required()
                        ->maxLength(200),

                    TextInput::make('config.company_description')
                        ->label('Mô tả công ty')
                        ->placeholder('Chuyên cung cấp thiết bị điện tử chất lượng cao')
                        ->maxLength(255),

                    TextInput::make('config.phone')
                        ->label('Số điện thoại')
                        ->placeholder('0913.718.995 - 0913.880.616')
                        ->maxLength(100),

                    TextInput::make('config.email')
                        ->label('Email')
                        ->placeholder('kinhdoanh@esat.vn')
                        ->maxLength(100),

                    TextInput::make('config.working_hours')
                        ->label('Giờ làm việc')
                        ->placeholder('7:30 - 17:00 (Thứ 2 - Thứ 6) & 7:30 - 12:00 (Thứ 7)')
                        ->maxLength(255),
                ])
                ->columns(2)
                ->collapsible()
                ->collapsed(),

            Section::make('Cột 2 - Chính sách & Mạng xã hội')
                ->schema([
                    TextInput::make('config.policy_title')
                        ->label('Tiêu đề cột chính sách')
                        ->default('Chính sách')
                        ->maxLength(50),

                    Section::make('3 Chính sách (cố định)')
                        ->schema([
                            TextInput::make('config.policy_1_label')
                                ->label('Chính sách 1')
                                ->placeholder('Chính sách & Điều khoản mua bán hàng hóa')
                                ->maxLength(100),
                            TextInput::make('config.policy_1_link')
                                ->label('Link')
                                ->placeholder('/bai-viet/chinh-sach'),

                            TextInput::make('config.policy_2_label')
                                ->label('Chính sách 2')
                                ->placeholder('Hệ thống đại lý & điểm bán hàng')
                                ->maxLength(100),
                            TextInput::make('config.policy_2_link')
                                ->label('Link')
                                ->placeholder('/bai-viet/he-thong-dai-ly'),

                            TextInput::make('config.policy_3_label')
                                ->label('Chính sách 3')
                                ->placeholder('Bảo mật & Quyền riêng tư')
                                ->maxLength(100),
                            TextInput::make('config.policy_3_link')
                                ->label('Link')
                                ->placeholder('/bai-viet/bao-mat'),
                        ])
                        ->columns(2),

                    TextInput::make('config.social_title')
                        ->label('Tiêu đề mạng xã hội')
                        ->default('Kết nối với chúng tôi')
                        ->maxLength(50),

                    Section::make('5 Mạng xã hội (cố định)')
                        ->schema([
                            TextInput::make('config.facebook_url')
                                ->label('Facebook')
                                ->placeholder('https://facebook.com/...'),
                            TextInput::make('config.zalo_url')
                                ->label('Zalo')
                                ->placeholder('https://zalo.me/...'),
                            TextInput::make('config.youtube_url')
                                ->label('YouTube')
                                ->placeholder('https://youtube.com/...'),
                            TextInput::make('config.tiktok_url')
                                ->label('TikTok')
                                ->placeholder('https://tiktok.com/...'),
                            TextInput::make('config.messenger_url')
                                ->label('Messenger')
                                ->placeholder('https://m.me/...'),
                        ])
                        ->columns(5),
                ])
                ->collapsible()
                ->collapsed(),

            Section::make('Cột 3 - Hiệp hội & Chứng nhận')
                ->schema([
                    TextInput::make('config.certification_title')
                        ->label('Tiêu đề cột')
                        ->default('Hiệp hội - Chứng nhận')
                        ->maxLength(50),

                    Section::make('Chứng nhận Bộ Công Thương')
                        ->schema([
                            FileUpload::make('config.bocongthuong_logo')
                                ->label('Logo Bộ Công Thương')
                                ->image()
                                ->directory('footer')
                                ->disk('public'),
                            TextInput::make('config.bocongthuong_text')
                                ->label('Text')
                                ->default('Đã đăng ký với Bộ Công Thương')
                                ->maxLength(100),
                            TextInput::make('config.bocongthuong_link')
                                ->label('Link')
                                ->placeholder('http://online.gov.vn/...'),
                        ])
                        ->columns(3),

                    TextInput::make('config.association_title')
                        ->label('Tiêu đề hiệp hội')
                        ->default('Thành viên các hiệp hội')
                        ->maxLength(50),

                    Section::make('4 Logo hiệp hội (cố định)')
                        ->schema([
                            FileUpload::make('config.association_1_logo')
                                ->label('Logo 1')
                                ->image()
                                ->directory('footer')
                                ->disk('public'),
                            TextInput::make('config.association_1_link')
                                ->label('Link 1')
                                ->placeholder('https://vcci.com.vn'),
                            FileUpload::make('config.association_2_logo')
                                ->label('Logo 2')
                                ->image()
                                ->directory('footer')
                                ->disk('public'),
                            TextInput::make('config.association_2_link')
                                ->label('Link 2')
                                ->placeholder('https://...'),
                            FileUpload::make('config.association_3_logo')
                                ->label('Logo 3')
                                ->image()
                                ->directory('footer')
                                ->disk('public'),
                            TextInput::make('config.association_3_link')
                                ->label('Link 3')
                                ->placeholder('https://...'),
                            FileUpload::make('config.association_4_logo')
                                ->label('Logo 4')
                                ->image()
                                ->directory('footer')
                                ->disk('public'),
                            TextInput::make('config.association_4_link')
                                ->label('Link 4')
                                ->placeholder('https://...'),
                        ])
                        ->columns(2),
                ])
                ->collapsible()
                ->collapsed(),

            Section::make('Copyright')
                ->schema([
                    TextInput::make('config.copyright')
                        ->label('Copyright')
                        ->default('© 2025 Copyright by ESAT - All Rights Reserved')
                        ->maxLength(200),
                ])
                ->collapsible()
                ->collapsed(),
        ];
    }
}
