<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\CatPost;
use Illuminate\Support\Str;

class PostNewsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Tạo chuyên mục tin tức nếu chưa có
        $newsCategory = CatPost::firstOrCreate([
            'slug' => 'tin-tuc'
        ], [
            'name' => 'Tin tức',
            'description' => 'Tin tức mới nhất từ Vũ Phúc Baking',
            'seo_title' => 'Tin tức - Vũ Phúc Baking',
            'seo_description' => 'Cập nhật tin tức mới nhất từ Vũ Phúc Baking',
            'order' => 2,
            'status' => 'active',
            'type' => 'news'
        ]);

        // Tạo các bài viết tin tức mẫu
        $news = [
            [
                'title' => 'Vũ Phúc Baking khai trương showroom mới tại TP.HCM',
                'slug' => 'vu-phuc-baking-khai-truong-showroom-moi-tai-tp-hcm',
                'content' => '<p>Vũ Phúc Baking vui mừng thông báo khai trương showroom mới tại trung tâm TP.HCM, mang đến cho khách hàng không gian trải nghiệm sản phẩm hiện đại và tiện lợi hơn.</p>
                
                <h3>Địa chỉ showroom mới:</h3>
                <p>123 Đường Nguyễn Huệ, Quận 1, TP.HCM</p>
                
                <h3>Ưu đãi khai trương:</h3>
                <ul>
                    <li>Giảm giá 20% cho tất cả sản phẩm</li>
                    <li>Tặng kèm voucher 500.000đ cho đơn hàng từ 2 triệu</li>
                    <li>Miễn phí giao hàng trong bán kính 10km</li>
                </ul>',
                'seo_title' => 'Vũ Phúc Baking khai trương showroom mới tại TP.HCM',
                'seo_description' => 'Vũ Phúc Baking khai trương showroom mới tại trung tâm TP.HCM với nhiều ưu đãi hấp dẫn.',
                'type' => 'news',
                'order' => 1
            ],
            [
                'title' => 'Ra mắt dòng sản phẩm bánh organic mới',
                'slug' => 'ra-mat-dong-san-pham-banh-organic-moi',
                'content' => '<p>Vũ Phúc Baking tự hào giới thiệu dòng sản phẩm bánh organic mới, được làm từ 100% nguyên liệu hữu cơ, đảm bảo an toàn cho sức khỏe.</p>
                
                <h3>Đặc điểm nổi bật:</h3>
                <ul>
                    <li>Nguyên liệu 100% hữu cơ</li>
                    <li>Không chất bảo quản</li>
                    <li>Không đường tinh luyện</li>
                    <li>Giàu chất xơ và vitamin</li>
                </ul>',
                'seo_title' => 'Ra mắt dòng sản phẩm bánh organic mới',
                'seo_description' => 'Vũ Phúc Baking ra mắt dòng bánh organic từ nguyên liệu hữu cơ 100%, an toàn cho sức khỏe.',
                'type' => 'news',
                'order' => 2
            ],
            [
                'title' => 'Chương trình đào tạo miễn phí cho sinh viên',
                'slug' => 'chuong-trinh-dao-tao-mien-phi-cho-sinh-vien',
                'content' => '<p>Vũ Phúc Baking phối hợp với các trường đại học tổ chức chương trình đào tạo miễn phí về kỹ thuật làm bánh cho sinh viên.</p>
                
                <h3>Nội dung chương trình:</h3>
                <ul>
                    <li>Kỹ thuật làm bánh cơ bản</li>
                    <li>Trang trí bánh chuyên nghiệp</li>
                    <li>Quản lý chất lượng sản phẩm</li>
                    <li>Thực tập tại nhà máy</li>
                </ul>',
                'seo_title' => 'Chương trình đào tạo miễn phí cho sinh viên',
                'seo_description' => 'Vũ Phúc Baking tổ chức chương trình đào tạo miễn phí về kỹ thuật làm bánh cho sinh viên.',
                'type' => 'news',
                'order' => 3
            ]
        ];

        foreach ($news as $newsData) {
            $post = Post::firstOrCreate([
                'slug' => $newsData['slug']
            ], array_merge($newsData, [
                'is_featured' => rand(0, 1),
                'status' => 'active'
            ]));

            // Attach category nếu chưa có
            if (!$post->categories()->where('cat_post_id', $newsCategory->id)->exists()) {
                $post->categories()->attach($newsCategory->id);
            }
        }

        $this->command->info('Đã tạo ' . count($news) . ' bài viết tin tức mẫu.');
    }
}
