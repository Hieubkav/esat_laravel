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
                'title' => 'ESAT',
                'subtitle' => 'Chuyên cung cấp thiết bị điện tử chất lượng cao',
                'content' => [
                    'description' => 'Cung cấp thiết bị điện tử, linh kiện công nghệ chính hãng',
                    'features' => ['Chất lượng cao', 'Giá cả hợp lý', 'Hỗ trợ kỹ thuật']
                ],
                'button_text' => 'Khám phá ngay',
                'button_url' => '/san-pham',
                'position' => 1,
            ],
            'about-us' => [
                'component_name' => 'Giới thiệu',
                'title' => 'Chào mừng đến với ESAT',
                'subtitle' => 'VỀ CHÚNG TÔI',
                'content' => [
                    'description' => 'Lấy khách hàng làm trọng tâm cho mọi hoạt động, chúng tôi luôn tiên phong trong việc cung cấp các sản phẩm thiết bị điện tử chất lượng cao với giá cả hợp lý.',
                    'quote' => 'Giá trị cốt lõi của chúng tôi là Vì sự phát triển của khách hàng',
                    'services' => [
                        ['title' => 'Thiết bị chính hãng', 'desc' => 'Sản phẩm chất lượng từ các thương hiệu uy tín'],
                        ['title' => 'Quy Trình Chuẩn', 'desc' => 'Kiểm soát chất lượng nghiêm ngặt'],
                        ['title' => 'Hỗ trợ kỹ thuật', 'desc' => 'Tư vấn và hỗ trợ chuyên nghiệp'],
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
                'title' => 'Hướng dẫn sử dụng',
                'subtitle' => 'Tài liệu hướng dẫn từ chuyên gia',
                'content' => [
                    'limit' => 6,
                    'show_duration' => true,
                    'show_price' => true
                ],
                'button_text' => 'Xem tất cả hướng dẫn',
                'button_url' => '/huong-dan',
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
                'title' => 'Bắt đầu hành trình<br>với <span class="italic">ESAT</span>',
                'subtitle' => 'Trải nghiệm đẳng cấp',
                'button_text' => 'Mua sắm ngay',
                'button_url' => '/san-pham',
                'position' => 10,
            ],
            'footer' => [
                'component_name' => 'Footer',
                'content' => [
                    'company_info' => [
                        'name' => 'ESAT',
                        'description' => 'Chất lượng tạo nên thương hiệu',
                    ],
                    'contact' => [
                        'phone' => '0913.718.995 - 0913.880.616',
                        'email' => 'kinhdoanh@esat.vn',
                        'hours' => '8:00 - 17:00 (Thứ 2 - Thứ 7)',
                    ],
                    'social_links' => [
                        'facebook' => '#',
                        'youtube' => '#',
                        'instagram' => '#',
                    ],
                    'policies' => [
                        ['title' => 'CHÍNH SÁCH & ĐIỀU KHOẢN MUA BÁN HÀNG HÓA', 'url' => '/chinh-sach'],
                        ['title' => 'BẢO MẬT & QUYỀN RIÊNG TƯ', 'url' => '/bao-mat'],
                    ],
                    'copyright' => '© 2025 Copyright by ESAT - All Rights Reserved',
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
