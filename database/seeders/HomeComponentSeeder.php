<?php

namespace Database\Seeders;

use App\Enums\HomeComponentType;
use App\Models\HomeComponent;
use Illuminate\Database\Seeder;

class HomeComponentSeeder extends Seeder
{
    public function run(): void
    {
        $components = [
            [
                'type' => HomeComponentType::HeroCarousel->value,
                'order' => 1,
                'active' => true,
                'config' => [
                    'slides' => [],
                ],
            ],
            [
                'type' => HomeComponentType::Stats->value,
                'order' => 2,
                'active' => true,
                'config' => [
                    'items' => [
                        ['value' => '8500+', 'label' => 'Khách hàng'],
                        ['value' => '150+', 'label' => 'Đối tác'],
                        ['value' => '1200+', 'label' => 'Sản phẩm'],
                        ['value' => '63', 'label' => 'Tỉnh thành'],
                    ],
                ],
            ],
            [
                'type' => HomeComponentType::About->value,
                'order' => 3,
                'active' => true,
                'config' => [
                    'badge' => 'VỀ CHÚNG TÔI',
                    'title' => 'Chào mừng đến với ESAT',
                    'description' => 'Lấy người tiêu dùng làm trọng tâm cho mọi hoạt động, chúng tôi luôn tiên phong trong việc tạo ra xu hướng tiêu dùng trong ngành thực phẩm và luôn sáng tạo để phục vụ người tiêu dùng.',
                    'quote' => 'Cam kết mang đến giải pháp tối ưu cho khách hàng.',
                    'button_text' => 'Tìm hiểu thêm về chúng tôi',
                    'button_url' => '/gioi-thieu',
                    'feature_1_title' => 'Sản phẩm cao cấp',
                    'feature_1_desc' => 'Chất lượng từ nguyên liệu tự nhiên',
                    'feature_2_title' => 'Quy trình chuẩn',
                    'feature_2_desc' => 'Kiểm soát chất lượng nghiêm ngặt',
                    'feature_3_title' => 'Hỗ trợ kỹ thuật',
                    'feature_3_desc' => 'Tư vấn và hỗ trợ chuyên nghiệp',
                    'feature_4_title' => 'Đội ngũ chuyên gia',
                    'feature_4_desc' => 'Kinh nghiệm nhiều năm trong ngành',
                ],
            ],
            [
                'type' => HomeComponentType::ProductCategories->value,
                'order' => 4,
                'active' => true,
                'config' => [
                    'title' => 'Danh mục sản phẩm',
                    'categories' => [],
                ],
            ],
            [
                'type' => HomeComponentType::FeaturedProducts->value,
                'order' => 5,
                'active' => true,
                'config' => [
                    'title' => 'Sản phẩm nổi bật',
                    'subtitle' => 'Những sản phẩm được yêu thích nhất',
                    'display_mode' => 'featured',
                    'limit' => 8,
                    'view_all_link' => '/san-pham',
                ],
            ],
            [
                'type' => HomeComponentType::Slogan->value,
                'order' => 6,
                'active' => true,
                'config' => [
                    'title' => 'Nhà Phân Phối Thiết Bị Công Nghệ',
                    'subtitle' => 'VÌ GIẢI PHÁP KẾT NỐI THÔNG MINH',
                ],
            ],
            [
                'type' => HomeComponentType::Partners->value,
                'order' => 7,
                'active' => true,
                'config' => [
                    'title' => 'Đối tác',
                    'display_mode' => 'auto',
                    'limit' => 10,
                    'auto_scroll' => true,
                ],
            ],
            [
                'type' => HomeComponentType::News->value,
                'order' => 8,
                'active' => true,
                'config' => [
                    'title' => 'Tin tức',
                    'display_mode' => 'latest',
                    'limit' => 6,
                    'view_all_link' => '/bai-viet',
                ],
            ],
            [
                'type' => HomeComponentType::Footer->value,
                'order' => 9,
                'active' => true,
                'config' => [
                    // CTA - Call to Action
                    'cta_badge' => 'GIẢI PHÁP CÔNG NGHỆ',
                    'cta_title' => 'Cùng kết nối công nghệ nâng tầm hiệu quả',
                    'cta_button_text' => 'Tìm hiểu ngay',
                    'cta_button_url' => '/lien-he',
                    // Cột 1 - Thông tin công ty
                    'company_name' => 'CÔNG TY TNHH ESAT',
                    'company_description' => 'Chuyên cung cấp thiết bị điện tử chất lượng cao',
                    'phone' => '0913.718.995 - 0913.880.616',
                    'email' => 'kinhdoanh@esat.vn',
                    'working_hours' => '7:30 - 17:00 (Thứ 2 - Thứ 6) & 7:30 - 12:00 (Thứ 7)',
                    // Cột 2 - Chính sách
                    'policy_title' => 'Chính sách',
                    'policy_1_label' => 'Chính sách & Điều khoản mua bán hàng hóa',
                    'policy_1_link' => '/bai-viet/chinh-sach',
                    'policy_2_label' => 'Hệ thống đại lý & điểm bán hàng',
                    'policy_2_link' => '/bai-viet/he-thong-dai-ly',
                    'policy_3_label' => 'Bảo mật & Quyền riêng tư',
                    'policy_3_link' => '/bai-viet/bao-mat',
                    'social_title' => 'Kết nối với chúng tôi',
                    'facebook_url' => 'https://facebook.com',
                    'zalo_url' => 'https://zalo.me',
                    'youtube_url' => 'https://youtube.com',
                    'tiktok_url' => 'https://tiktok.com',
                    'messenger_url' => 'https://m.me',
                    // Cột 3 - Hiệp hội & Chứng nhận
                    'certification_title' => 'Hiệp hội - Chứng nhận',
                    'bocongthuong_text' => 'Đã đăng ký với Bộ Công Thương',
                    'association_title' => 'Thành viên các hiệp hội',
                    // Copyright
                    'copyright' => '© 2025 Copyright by ESAT - All Rights Reserved',
                ],
            ],
        ];

        foreach ($components as $component) {
            HomeComponent::updateOrCreate(
                ['type' => $component['type']],
                [
                    'order' => $component['order'],
                    'active' => $component['active'],
                    'config' => $component['config'],
                ]
            );
        }
    }
}
