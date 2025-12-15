<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\CatPost;
use Illuminate\Support\Str;

class PostServiceSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Tạo chuyên mục dịch vụ nếu chưa có
        $serviceCategory = CatPost::firstOrCreate([
            'slug' => 'dich-vu'
        ], [
            'name' => 'Dịch vụ',
            'description' => 'Các dịch vụ của Vũ Phúc Baking',
            'seo_title' => 'Dịch vụ - Vũ Phúc Baking',
            'seo_description' => 'Khám phá các dịch vụ chuyên nghiệp của Vũ Phúc Baking',
            'order' => 1,
            'status' => 'active',
            'type' => 'service'
        ]);

        // Tạo các bài viết dịch vụ mẫu
        $services = [
            [
                'title' => 'Cung cấp nguyên liệu làm bánh',
                'slug' => 'cung-cap-nguyen-lieu-lam-banh',
                'content' => '<p>Vũ Phúc Baking cung cấp đầy đủ nguyên liệu làm bánh chất lượng cao từ các thương hiệu uy tín trong và ngoài nước. Chúng tôi cam kết mang đến cho khách hàng những sản phẩm tốt nhất với giá cả hợp lý.</p>
                
                <h3>Các loại nguyên liệu chúng tôi cung cấp:</h3>
                <ul>
                    <li>Bột mì các loại (bột mì đa dụng, bột mì bánh mì, bột mì bánh ngọt)</li>
                    <li>Đường các loại (đường trắng, đường nâu, đường icing)</li>
                    <li>Bơ và margarine chất lượng cao</li>
                    <li>Trứng tươi và các sản phẩm từ trứng</li>
                    <li>Hương liệu và phẩm màu thực phẩm</li>
                    <li>Chocolate và compound chocolate</li>
                </ul>',
                'seo_title' => 'Cung cấp nguyên liệu làm bánh chất lượng cao',
                'seo_description' => 'Vũ Phúc Baking cung cấp đầy đủ nguyên liệu làm bánh chất lượng cao từ các thương hiệu uy tín với giá cả hợp lý.',
                'type' => 'service',
                'order' => 1
            ],
            [
                'title' => 'Thiết bị và máy móc làm bánh',
                'slug' => 'thiet-bi-va-may-moc-lam-banh',
                'content' => '<p>Chúng tôi cung cấp đầy đủ thiết bị và máy móc làm bánh chuyên nghiệp cho cả cá nhân và doanh nghiệp. Từ những dụng cụ cơ bản đến các máy móc hiện đại, chúng tôi có tất cả.</p>
                
                <h3>Thiết bị chúng tôi cung cấp:</h3>
                <ul>
                    <li>Lò nướng các loại (lò điện, lò gas, lò đối lưu)</li>
                    <li>Máy trộn bột công nghiệp</li>
                    <li>Máy cán bột và máy chia bột</li>
                    <li>Tủ ủ bột và tủ lạnh chuyên dụng</li>
                    <li>Các dụng cụ làm bánh cơ bản</li>
                    <li>Khuôn bánh các loại</li>
                </ul>',
                'seo_title' => 'Thiết bị máy móc làm bánh chuyên nghiệp',
                'seo_description' => 'Cung cấp thiết bị và máy móc làm bánh chuyên nghiệp cho cá nhân và doanh nghiệp với chất lượng tốt nhất.',
                'type' => 'service',
                'order' => 2
            ],
            [
                'title' => 'Đào tạo kỹ thuật làm bánh',
                'slug' => 'dao-tao-ky-thuat-lam-banh',
                'content' => '<p>Vũ Phúc Baking tổ chức các khóa học đào tạo kỹ thuật làm bánh từ cơ bản đến nâng cao. Đội ngũ giảng viên giàu kinh nghiệm sẽ hướng dẫn bạn từng bước một cách chi tiết.</p>
                
                <h3>Các khóa học chúng tôi cung cấp:</h3>
                <ul>
                    <li>Khóa học làm bánh cơ bản</li>
                    <li>Khóa học làm bánh mì chuyên nghiệp</li>
                    <li>Khóa học làm bánh ngọt và bánh kem</li>
                    <li>Khóa học trang trí bánh</li>
                    <li>Khóa học quản lý tiệm bánh</li>
                </ul>',
                'seo_title' => 'Đào tạo kỹ thuật làm bánh chuyên nghiệp',
                'seo_description' => 'Khóa học đào tạo kỹ thuật làm bánh từ cơ bản đến nâng cao với đội ngũ giảng viên giàu kinh nghiệm.',
                'type' => 'service',
                'order' => 3
            ],
            [
                'title' => 'Tư vấn kỹ thuật và công thức',
                'slug' => 'tu-van-ky-thuat-va-cong-thuc',
                'content' => '<p>Đội ngũ chuyên gia của Vũ Phúc Baking luôn sẵn sàng tư vấn kỹ thuật và chia sẻ công thức làm bánh cho khách hàng. Chúng tôi cam kết hỗ trợ bạn giải quyết mọi vấn đề trong quá trình sản xuất.</p>
                
                <h3>Dịch vụ tư vấn bao gồm:</h3>
                <ul>
                    <li>Tư vấn công thức làm bánh</li>
                    <li>Hỗ trợ khắc phục sự cố trong sản xuất</li>
                    <li>Tư vấn quy trình sản xuất</li>
                    <li>Hướng dẫn sử dụng thiết bị</li>
                    <li>Tư vấn kinh doanh tiệm bánh</li>
                </ul>',
                'seo_title' => 'Tư vấn kỹ thuật và công thức làm bánh',
                'seo_description' => 'Dịch vụ tư vấn kỹ thuật và chia sẻ công thức làm bánh từ đội ngũ chuyên gia giàu kinh nghiệm.',
                'type' => 'service',
                'order' => 4
            ],
            [
                'title' => 'Giao hàng tận nơi',
                'slug' => 'giao-hang-tan-noi',
                'content' => '<p>Vũ Phúc Baking cung cấp dịch vụ giao hàng tận nơi nhanh chóng và đảm bảo chất lượng sản phẩm. Chúng tôi có đội ngũ giao hàng chuyên nghiệp và hệ thống bảo quản hiện đại.</p>
                
                <h3>Ưu điểm dịch vụ giao hàng:</h3>
                <ul>
                    <li>Giao hàng nhanh chóng trong ngày</li>
                    <li>Đảm bảo chất lượng sản phẩm</li>
                    <li>Hệ thống bảo quản lạnh</li>
                    <li>Đội ngũ giao hàng chuyên nghiệp</li>
                    <li>Phí giao hàng hợp lý</li>
                </ul>',
                'seo_title' => 'Dịch vụ giao hàng tận nơi nhanh chóng',
                'seo_description' => 'Dịch vụ giao hàng tận nơi nhanh chóng, đảm bảo chất lượng sản phẩm với đội ngũ chuyên nghiệp.',
                'type' => 'service',
                'order' => 5
            ]
        ];

        foreach ($services as $serviceData) {
            $post = Post::firstOrCreate([
                'slug' => $serviceData['slug']
            ], array_merge($serviceData, [
                'is_featured' => true,
                'status' => 'active'
            ]));

            // Attach category nếu chưa có
            if (!$post->categories()->where('cat_post_id', $serviceCategory->id)->exists()) {
                $post->categories()->attach($serviceCategory->id);
            }
        }

        $this->command->info('Đã tạo ' . count($services) . ' bài viết dịch vụ mẫu.');
    }
}
