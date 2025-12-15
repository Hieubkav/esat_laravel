<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebDesign extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_name',
        'component_key',
        'title',
        'subtitle',
        'content',
        'image_url',
        'video_url',
        'button_text',
        'button_url',
        'position',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Các component mặc định cho storefront
     */
    public static function getDefaultComponents(): array
    {
        return [
            'hero-banner' => [
                'component_name' => 'Hero Banner',
                'title' => 'Vũ Phúc Baking',
                'subtitle' => 'Nhà phân phối độc quyền Rich Products Vietnam tại ĐBSCL',
                'content' => [
                    'description' => 'Cung cấp nguyên liệu, dụng cụ và thiết bị làm bánh chuyên nghiệp',
                    'features' => ['Chất lượng cao', 'Giá cả hợp lý', 'Hỗ trợ kỹ thuật']
                ],
                'button_text' => 'Khám phá ngay',
                'button_url' => '/ban-hang',
                'position' => 1,
            ],
            'about-us' => [
                'component_name' => 'Giới thiệu',
                'title' => 'Chào mừng đến với Vuphuc Baking®',
                'subtitle' => 'VỀ CHÚNG TÔI',
                'content' => [
                    'description' => 'Lấy người tiêu dùng làm trọng tâm cho mọi hoạt động, chúng tôi luôn tiên phong trong việc tạo ra xu hướng tiêu dùng trong ngành thực phẩm và luôn sáng tạo để phục vụ người tiêu dùng tạo ra những sản phẩm an toàn, chất lượng và hướng đến mục tiêu phát triển bền vững.',
                    'quote' => 'Giá trị cốt lõi của chúng tôi là Vì sự phát triển của khách hàng',
                    'services' => [
                        ['title' => 'Bánh Ngọt Cao Cấp', 'desc' => 'Sản phẩm chất lượng từ nguyên liệu tự nhiên'],
                        ['title' => 'Quy Trình Chuẩn', 'desc' => 'Kiểm soát chất lượng nghiêm ngặt'],
                        ['title' => 'Đào Tạo Chuyên Nghiệp', 'desc' => 'Hỗ trợ kỹ thuật và đào tạo'],
                        ['title' => 'Đội Ngũ Chuyên Gia', 'desc' => 'Kinh nghiệm nhiều năm trong ngành']
                    ]
                ],
                'button_text' => 'Tìm hiểu thêm về chúng tôi',
                'button_url' => '/gioi-thieu',
                'position' => 2,
            ],
            'stats-counter' => [
                'component_name' => 'Thống kê',
                'title' => 'Con số ấn tượng',
                'content' => [
                    'stats' => [
                        ['number' => '500+', 'label' => 'Khách hàng tin tưởng'],
                        ['number' => '1000+', 'label' => 'Sản phẩm chất lượng'],
                        ['number' => '10+', 'label' => 'Năm kinh nghiệm'],
                        ['number' => '24/7', 'label' => 'Hỗ trợ khách hàng']
                    ]
                ],
                'position' => 3,
            ],
            'featured-products' => [
                'component_name' => 'Sản phẩm nổi bật',
                'title' => 'Sản phẩm nổi bật',
                'subtitle' => 'Những sản phẩm được khách hàng yêu thích nhất',
                'content' => [
                    'limit' => 8,
                    'show_price' => true,
                    'show_add_to_cart' => true
                ],
                'button_text' => 'Xem tất cả hàng hóa',
                'button_url' => '/kho-hang',
                'position' => 4,
            ],
            'services' => [
                'component_name' => 'Dịch vụ',
                'title' => 'Dịch vụ của chúng tôi',
                'subtitle' => 'Cam kết mang đến dịch vụ tốt nhất',
                'content' => [
                    'services' => [
                        ['title' => 'Tư vấn kỹ thuật', 'desc' => 'Hỗ trợ kỹ thuật chuyên nghiệp'],
                        ['title' => 'Đào tạo', 'desc' => 'Đào tạo kỹ năng làm bánh'],
                        ['title' => 'Giao hàng', 'desc' => 'Giao hàng nhanh chóng']
                    ]
                ],
                'position' => 5,
            ],
            'slogan' => [
                'component_name' => 'Slogan',
                'title' => 'Chất lượng - Uy tín - Chuyên nghiệp',
                'subtitle' => 'Đối tác tin cậy của bạn',
                'position' => 6,
            ],
            'courses-overview' => [
                'component_name' => 'Tổng quan khóa học',
                'title' => 'Khóa học làm bánh',
                'subtitle' => 'Học từ những chuyên gia hàng đầu',
                'content' => [
                    'limit' => 6,
                    'show_duration' => true,
                    'show_price' => true
                ],
                'button_text' => 'Xem tất cả khóa học',
                'button_url' => '/khoa-hoc',
                'position' => 7,
            ],
            'partners' => [
                'component_name' => 'Đối tác',
                'title' => 'Đối tác của chúng tôi',
                'subtitle' => 'Những thương hiệu uy tín',
                'content' => [
                    'auto_scroll' => true,
                    'items_per_row' => 6
                ],
                'position' => 8,
            ],
            'blog-posts' => [
                'component_name' => 'Bài viết',
                'title' => 'Tin tức & Bài viết',
                'subtitle' => 'Cập nhật thông tin mới nhất',
                'content' => [
                    'limit' => 6,
                    'show_excerpt' => true,
                    'show_author' => true
                ],
                'button_text' => 'Xem tất cả bài viết',
                'button_url' => '/bai-viet',
                'position' => 9,
            ],
            'homepage-cta' => [
                'component_name' => 'Global CTA',
                'title' => 'Bắt đầu hành trình<br>với <span class="italic">Vũ Phúc Baking</span>',
                'subtitle' => 'Trải nghiệm đẳng cấp',
                'button_text' => 'Mua sắm ngay',
                'button_url' => '/shop',
                'position' => 10,
            ],
            'footer' => [
                'component_name' => 'Footer',
                'content' => [
                    'company_info' => [
                        'name' => 'VUPHUC BAKING',
                        'description' => 'Chất lượng tạo nên thương hiệu',
                        'license' => 'Giấy phép kinh doanh số 1800935879 cấp ngày 29/4/2009',
                        'director' => 'Chịu trách nhiệm nội dung: Trần Uy Vũ - Tổng Giám đốc',
                    ],
                    'contact' => [
                        'address' => '19-21 Đường B17, Khu Dân Cư Hưng Phú 1, Phường Hưng Phú, Quận Cái Răng, TP Cần Thơ',
                        'phone' => '1900636340',
                        'email' => 'contact@vuphucbaking.com',
                        'hours' => '6:00 - 22:00 (Thứ 2 - Chủ nhật)',
                    ],
                    'social_links' => [
                        'facebook' => '#',
                        'youtube' => '#',
                        'instagram' => '#',
                    ],
                    'certifications' => [
                        'CBA',
                        'Hội Nữ Doanh Nhân TP.Cần Thơ',
                        'Mạng lưới Doanh nghiệp Thích ứng Đồng bằng sông Cửu Long',
                    ],
                    'policies' => [
                        ['title' => 'CHÍNH SÁCH & ĐIỀU KHOẢN MUA BÁN HÀNG HÓA', 'url' => '/chinh-sach'],
                        ['title' => 'HỆ THỐNG ĐẠI LÝ & ĐIỂM BÁN HÀNG', 'url' => '/he-thong-dai-ly'],
                        ['title' => 'BẢO MẬT & QUYỀN RIÊNG TƯ', 'url' => '/bao-mat'],
                    ],
                    'copyright' => '© 2025 Copyright by VUPHUC BAKING - All Rights Reserved',
                ],
                'position' => 11,
            ],
        ];
    }

    /**
     * Lấy cấu hình theo component key
     */
    public static function getByComponent(string $componentKey): ?self
    {
        return static::where('component_key', $componentKey)->first();
    }

    /**
     * Kiểm tra component có hiển thị không
     */
    public static function isVisible(string $componentKey): bool
    {
        $design = static::getByComponent($componentKey);

        if (!$design) {
            return true; // Mặc định hiển thị nếu chưa có cấu hình
        }

        return $design->is_active;
    }

    /**
     * Lấy tất cả components theo thứ tự
     */
    public static function getOrderedComponents(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Tạo hoặc cập nhật component
     */
    public static function updateOrCreateComponent(string $componentKey, array $data): self
    {
        return static::updateOrCreate(
            ['component_key' => $componentKey],
            $data
        );
    }

    /**
     * Reset về cấu hình mặc định
     */
    public static function resetToDefault(): void
    {
        static::truncate();

        foreach (static::getDefaultComponents() as $key => $config) {
            static::create([
                'component_key' => $key,
                'component_name' => $config['component_name'],
                'title' => $config['title'] ?? null,
                'subtitle' => $config['subtitle'] ?? null,
                'content' => $config['content'] ?? null,
                'image_url' => $config['image_url'] ?? null,
                'video_url' => $config['video_url'] ?? null,
                'button_text' => $config['button_text'] ?? null,
                'button_url' => $config['button_url'] ?? null,
                'position' => $config['position'],
                'is_active' => true,
                'settings' => [],
            ]);
        }
    }
}
