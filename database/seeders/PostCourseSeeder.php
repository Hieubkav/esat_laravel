<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\CatPost;
use Illuminate\Support\Str;

class PostCourseSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Tạo chuyên mục khóa học nếu chưa có
        $courseCategory = CatPost::firstOrCreate([
            'slug' => 'khoa-hoc'
        ], [
            'name' => 'Khóa học',
            'description' => 'Các khóa học chuyên nghiệp của Vũ Phúc Baking Academy',
            'seo_title' => 'Khóa học - Vũ Phúc Baking Academy',
            'seo_description' => 'Khám phá các khóa học làm bánh chuyên nghiệp tại Vũ Phúc Baking Academy',
            'order' => 3,
            'status' => 'active',
            'type' => 'course'
        ]);

        $courses = [
            [
                'title' => 'Khóa học làm bánh cơ bản cho người mới bắt đầu',
                'slug' => 'khoa-hoc-lam-banh-co-ban-cho-nguoi-moi-bat-dau',
                'content' => '<h2>Giới thiệu khóa học</h2>
                <p>Khóa học làm bánh cơ bản dành cho những người mới bắt đầu muốn tìm hiểu về nghệ thuật làm bánh. Trong khóa học này, bạn sẽ được học các kỹ thuật cơ bản nhất để có thể tự tin làm những chiếc bánh đầu tiên của mình.</p>
                
                <h3>Nội dung khóa học</h3>
                <ul>
                    <li>Tìm hiểu về các loại bột, đường và nguyên liệu cơ bản</li>
                    <li>Kỹ thuật trộn bột và nhào bột đúng cách</li>
                    <li>Làm bánh mì cơ bản</li>
                    <li>Làm bánh quy butter cookies</li>
                    <li>Làm bánh cupcake đơn giản</li>
                    <li>Trang trí bánh cơ bản</li>
                </ul>
                
                <h3>Thời gian học</h3>
                <p>Khóa học kéo dài 4 tuần, mỗi tuần 2 buổi, mỗi buổi 3 tiếng.</p>
                
                <h3>Học phí</h3>
                <p>2.500.000 VNĐ (bao gồm nguyên liệu và dụng cụ)</p>',
                'type' => 'course',
                'seo_title' => 'Khóa học làm bánh cơ bản - Vũ Phúc Baking Academy',
                'seo_description' => 'Tham gia khóa học làm bánh cơ bản tại Vũ Phúc Baking Academy. Học các kỹ thuật làm bánh từ A-Z cho người mới bắt đầu.',
            ],
            [
                'title' => 'Khóa học làm bánh ngọt nâng cao',
                'slug' => 'khoa-hoc-lam-banh-ngot-nang-cao',
                'content' => '<h2>Giới thiệu khóa học</h2>
                <p>Khóa học làm bánh ngọt nâng cao dành cho những người đã có kiến thức cơ bản về làm bánh và muốn nâng cao kỹ năng của mình. Bạn sẽ học cách làm những loại bánh phức tạp và tinh tế hơn.</p>
                
                <h3>Nội dung khóa học</h3>
                <ul>
                    <li>Làm bánh kem sinh nhật chuyên nghiệp</li>
                    <li>Kỹ thuật làm bánh macaron</li>
                    <li>Làm bánh tiramisu và các loại bánh mousse</li>
                    <li>Trang trí bánh với fondant</li>
                    <li>Làm bánh cưới đơn giản</li>
                    <li>Kỹ thuật piping và trang trí nâng cao</li>
                </ul>
                
                <h3>Yêu cầu</h3>
                <p>Đã hoàn thành khóa học cơ bản hoặc có kinh nghiệm làm bánh tương đương.</p>
                
                <h3>Thời gian học</h3>
                <p>Khóa học kéo dài 6 tuần, mỗi tuần 3 buổi, mỗi buổi 4 tiếng.</p>
                
                <h3>Học phí</h3>
                <p>4.500.000 VNĐ (bao gồm nguyên liệu và dụng cụ chuyên dụng)</p>',
                'type' => 'course',
                'seo_title' => 'Khóa học làm bánh ngọt nâng cao - Vũ Phúc Baking Academy',
                'seo_description' => 'Nâng cao kỹ năng làm bánh với khóa học bánh ngọt nâng cao tại Vũ Phúc Baking Academy. Học làm macaron, bánh kem chuyên nghiệp.',
            ],
            [
                'title' => 'Khóa học làm bánh mì artisan',
                'slug' => 'khoa-hoc-lam-banh-mi-artisan',
                'content' => '<h2>Giới thiệu khóa học</h2>
                <p>Khóa học làm bánh mì artisan sẽ dạy bạn cách làm những ổ bánh mì thủ công chất lượng cao, từ việc nuôi men tự nhiên đến các kỹ thuật nướng chuyên nghiệp.</p>
                
                <h3>Nội dung khóa học</h3>
                <ul>
                    <li>Nuôi và duy trì men tự nhiên (sourdough starter)</li>
                    <li>Kỹ thuật nhào bột bằng tay</li>
                    <li>Làm bánh mì sourdough cổ điển</li>
                    <li>Bánh mì baguette Pháp</li>
                    <li>Bánh mì ngũ cốc và hạt</li>
                    <li>Kỹ thuật tạo hình và ghi điểm</li>
                    <li>Nướng bánh với đá và hơi nước</li>
                </ul>
                
                <h3>Thời gian học</h3>
                <p>Khóa học kéo dài 5 tuần, mỗi tuần 2 buổi, mỗi buổi 5 tiếng.</p>
                
                <h3>Học phí</h3>
                <p>3.800.000 VNĐ (bao gồm starter kit và nguyên liệu)</p>',
                'type' => 'course',
                'seo_title' => 'Khóa học làm bánh mì artisan - Vũ Phúc Baking Academy',
                'seo_description' => 'Học làm bánh mì artisan chuyên nghiệp tại Vũ Phúc Baking Academy. Từ nuôi men tự nhiên đến nướng bánh hoàn hảo.',
            ],
            [
                'title' => 'Khóa học trang trí bánh chuyên nghiệp',
                'slug' => 'khoa-hoc-trang-tri-banh-chuyen-nghiep',
                'content' => '<h2>Giới thiệu khóa học</h2>
                <p>Khóa học trang trí bánh chuyên nghiệp tập trung vào các kỹ thuật trang trí bánh nâng cao, giúp bạn tạo ra những chiếc bánh đẹp mắt và ấn tượng.</p>
                
                <h3>Nội dung khóa học</h3>
                <ul>
                    <li>Kỹ thuật piping với nhiều loại đuôi khác nhau</li>
                    <li>Làm hoa kem bơ chuyên nghiệp</li>
                    <li>Trang trí với chocolate và isomalt</li>
                    <li>Kỹ thuật airbrushing</li>
                    <li>Làm figurine và mô hình 3D</li>
                    <li>Thiết kế và lên ý tưởng trang trí</li>
                </ul>
                
                <h3>Thời gian học</h3>
                <p>Khóa học kéo dài 4 tuần, mỗi tuần 3 buổi, mỗi buổi 3 tiếng.</p>
                
                <h3>Học phí</h3>
                <p>3.200.000 VNĐ (bao gồm dụng cụ trang trí và nguyên liệu)</p>',
                'type' => 'course',
                'seo_title' => 'Khóa học trang trí bánh chuyên nghiệp - Vũ Phúc Baking Academy',
                'seo_description' => 'Học trang trí bánh chuyên nghiệp tại Vũ Phúc Baking Academy. Kỹ thuật piping, làm hoa kem, trang trí 3D.',
            ],
            [
                'title' => 'Khóa học kinh doanh tiệm bánh',
                'slug' => 'khoa-hoc-kinh-doanh-tiem-banh',
                'content' => '<h2>Giới thiệu khóa học</h2>
                <p>Khóa học kinh doanh tiệm bánh không chỉ dạy kỹ thuật làm bánh mà còn chia sẻ kinh nghiệm kinh doanh, quản lý và marketing cho tiệm bánh.</p>
                
                <h3>Nội dung khóa học</h3>
                <ul>
                    <li>Lập kế hoạch kinh doanh tiệm bánh</li>
                    <li>Tính toán chi phí và định giá sản phẩm</li>
                    <li>Quản lý nguyên liệu và kho hàng</li>
                    <li>Marketing online và offline</li>
                    <li>Xây dựng thương hiệu cá nhân</li>
                    <li>Quản lý đơn hàng và khách hàng</li>
                    <li>Các loại bánh bán chạy và xu hướng thị trường</li>
                </ul>
                
                <h3>Thời gian học</h3>
                <p>Khóa học kéo dài 3 tuần, mỗi tuần 2 buổi, mỗi buổi 4 tiếng.</p>
                
                <h3>Học phí</h3>
                <p>2.800.000 VNĐ (bao gồm tài liệu và template kinh doanh)</p>',
                'type' => 'course',
                'seo_title' => 'Khóa học kinh doanh tiệm bánh - Vũ Phúc Baking Academy',
                'seo_description' => 'Học kinh doanh tiệm bánh hiệu quả tại Vũ Phúc Baking Academy. Từ lập kế hoạch đến marketing và quản lý.',
            ],
        ];

        foreach ($courses as $courseData) {
            $post = Post::firstOrCreate([
                'slug' => $courseData['slug']
            ], array_merge($courseData, [
                'is_featured' => true,
                'status' => 'active'
            ]));

            // Attach category nếu chưa có
            if (!$post->categories()->where('cat_post_id', $courseCategory->id)->exists()) {
                $post->categories()->attach($courseCategory->id);
            }
        }

        $this->command->info('Đã tạo ' . count($courses) . ' bài viết khóa học mẫu.');
    }
}
