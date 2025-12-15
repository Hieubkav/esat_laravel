<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\CatProduct;
use App\Models\CatPost;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\Association;
use App\Models\Employee;
use App\Models\EmployeeImage;
use App\Models\Slider;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class FirstSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo User admin
        $this->createUsers();

        // 2. Tạo Settings
        $this->createSettings();

        // 3. Tạo Categories
        $catProducts = $this->createCatProducts();
        $catPosts = $this->createCatPosts();

        // 4. Tạo Products và ProductImages
        $this->createProducts($catProducts);

        // 5. Tạo Posts và PostImages
        $this->createPosts($catPosts);

        // 6. Tạo Customers
        $this->createCustomers();

        // 7. Tạo Partners
        $this->createPartners();

        // 8. Tạo Associations
        $this->createAssociations();

        // 9. Tạo Employees
        $this->createEmployees();

        // 10. Tạo Sliders
        $this->createSliders();

        // 11. Tạo MenuItems
        $this->createMenuItems($catProducts, $catPosts);

        // 12. Gọi các seeder khác
        $this->call([
            PostNewsSeeder::class,
            PostServiceSeeder::class,
            PostCourseSeeder::class,
        ]);
    }

    private function createUsers()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@vuphucbaking.com',
            'password' => 'password',
            'order' => 1,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Editor',
            'email' => 'editor@vuphucbaking.com',
            'password' => 'password',
            'order' => 2,
            'status' => 'active',
        ]);
    }

    private function createSettings()
    {
        Setting::create([
            'site_name' => 'Vũ Phúc Bakery',
            'logo_link' => '/images/logo.png',
            'favicon_link' => '/images/favicon.ico',
            'seo_title' => 'Vũ Phúc Bakery - Bánh ngon mỗi ngày',
            'seo_description' => 'Vũ Phúc Bakery chuyên sản xuất và cung cấp các loại bánh tươi ngon, chất lượng cao với nguyên liệu tự nhiên.',
            'og_image_link' => '/images/og-image.jpg',
            'hotline' => '0123456789',
            'address' => '123 Đường ABC, Quận XYZ, TP.HCM',
            'email' => 'info@vuphucbaking.com',
            'slogan' => 'Bánh ngon mỗi ngày',
            'facebook_link' => 'https://facebook.com/vuphucbaking',
            'zalo_link' => 'https://zalo.me/vuphucbaking',
            'youtube_link' => 'https://youtube.com/vuphucbaking',
            'tiktok_link' => 'https://tiktok.com/@vuphucbaking',
            'working_hours' => '6:00 - 22:00 (Thứ 2 - Chủ nhật)',
            'dmca_link' => 'https://dmca.com/vuphucbaking',
            'footer_description' => '',
            'order' => 1,
            'status' => 'active',
        ]);
    }

    private function createCatProducts()
    {
        $categories = [
            [
                'name' => 'Bánh Mì',
                'slug' => 'banh-mi',
                'seo_title' => 'Bánh Mì Tươi Ngon - Vũ Phúc Bakery',
                'seo_description' => 'Bánh mì tươi ngon được làm từ nguyên liệu tự nhiên, đảm bảo chất lượng và hương vị tuyệt vời.',
                'image' => '/images/categories/banh-mi.jpg',
                'description' => 'Các loại bánh mì tươi ngon, đa dạng hương vị',
                'order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Bánh Ngọt',
                'slug' => 'banh-ngot',
                'seo_title' => 'Bánh Ngọt Cao Cấp - Vũ Phúc Bakery',
                'seo_description' => 'Bánh ngọt cao cấp với nhiều hương vị độc đáo, phù hợp cho mọi dịp đặc biệt.',
                'image' => '/images/categories/banh-ngot.jpg',
                'description' => 'Bánh ngọt cao cấp, đa dạng mẫu mã',
                'order' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Bánh Kem',
                'slug' => 'banh-kem',
                'seo_title' => 'Bánh Kem Sinh Nhật - Vũ Phúc Bakery',
                'seo_description' => 'Bánh kem sinh nhật đẹp mắt, ngon miệng với nhiều mẫu mã và kích thước khác nhau.',
                'image' => '/images/categories/banh-kem.jpg',
                'description' => 'Bánh kem sinh nhật, bánh kem trang trí',
                'order' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Nguyên Liệu',
                'slug' => 'nguyen-lieu',
                'seo_title' => 'Nguyên Liệu Làm Bánh - Vũ Phúc Bakery',
                'seo_description' => 'Nguyên liệu làm bánh chất lượng cao, đảm bảo an toàn thực phẩm.',
                'image' => '/images/categories/nguyen-lieu.jpg',
                'description' => 'Nguyên liệu làm bánh chất lượng cao',
                'order' => 4,
                'status' => 'active',
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $createdCategories[] = CatProduct::create($category);
        }

        return $createdCategories;
    }

    private function createCatPosts()
    {
        $categories = [
            [
                'name' => 'Tin Tức',
                'slug' => 'tin-tuc',
                'seo_title' => 'Tin Tức - Vũ Phúc Bakery',
                'seo_description' => 'Cập nhật tin tức mới nhất về sản phẩm và hoạt động của Vũ Phúc Bakery.',
                'image' => '/images/categories/tin-tuc.jpg',
                'description' => 'Tin tức và cập nhật mới nhất',
                'order' => 1,
                'status' => 'active',
                'type' => 'news',
            ],
            [
                'name' => 'Hướng Dẫn',
                'slug' => 'huong-dan',
                'seo_title' => 'Hướng Dẫn Làm Bánh - Vũ Phúc Bakery',
                'seo_description' => 'Hướng dẫn chi tiết cách làm bánh tại nhà với các công thức đơn giản.',
                'image' => '/images/categories/huong-dan.jpg',
                'description' => 'Hướng dẫn làm bánh tại nhà',
                'order' => 2,
                'status' => 'active',
                'type' => 'normal',
            ],
            [
                'name' => 'Khuyến Mãi',
                'slug' => 'khuyen-mai',
                'seo_title' => 'Khuyến Mãi - Vũ Phúc Bakery',
                'seo_description' => 'Thông tin về các chương trình khuyến mãi hấp dẫn tại Vũ Phúc Bakery.',
                'image' => '/images/categories/khuyen-mai.jpg',
                'description' => 'Chương trình khuyến mãi hấp dẫn',
                'order' => 3,
                'status' => 'active',
                'type' => 'normal',
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $createdCategories[] = CatPost::create($category);
        }

        return $createdCategories;
    }

    private function createProducts($catProducts)
    {
        $products = [
            [
                'name' => 'Bánh Mì Việt Nam',
                'description' => 'Bánh mì truyền thống Việt Nam với nhân thịt nguội, pate và rau sống tươi ngon.',
                'seo_title' => 'Bánh Mì Việt Nam Truyền Thống - Vũ Phúc Bakery',
                'seo_description' => 'Bánh mì Việt Nam truyền thống với vỏ bánh giòn tan, nhân đầy đặn và hương vị đặc trưng.',
                'slug' => 'banh-mi-viet-nam',
                'price' => 25000,
                'compare_price' => 30000,
                'brand' => 'Vũ Phúc',
                'sku' => 'BM001',
                'stock' => 100,
                'unit' => 'chiếc',
                'is_hot' => true,
                'order' => 1,
                'status' => 'active',
                'category_id' => $catProducts[0]->id,
            ],
            [
                'name' => 'Bánh Croissant Bơ',
                'description' => 'Bánh croissant bơ thơm ngon với lớp vỏ giòn tan và nhân bơ béo ngậy.',
                'seo_title' => 'Bánh Croissant Bơ Pháp - Vũ Phúc Bakery',
                'seo_description' => 'Bánh croissant bơ kiểu Pháp với hương vị thơm ngon, lớp vỏ giòn tan đặc trưng.',
                'slug' => 'banh-croissant-bo',
                'price' => 35000,
                'brand' => 'Vũ Phúc',
                'sku' => 'BN001',
                'stock' => 50,
                'unit' => 'chiếc',
                'is_hot' => false,
                'order' => 2,
                'status' => 'active',
                'category_id' => $catProducts[1]->id,
            ],
            [
                'name' => 'Bánh Kem Sinh Nhật',
                'description' => 'Bánh kem sinh nhật với nhiều mẫu mã đẹp mắt, phù hợp cho mọi dịp đặc biệt.',
                'seo_title' => 'Bánh Kem Sinh Nhật Đẹp - Vũ Phúc Bakery',
                'seo_description' => 'Bánh kem sinh nhật đẹp mắt với nhiều kích thước và mẫu mã, đặt theo yêu cầu.',
                'slug' => 'banh-kem-sinh-nhat',
                'price' => 250000,
                'compare_price' => 300000,
                'brand' => 'Vũ Phúc',
                'sku' => 'BK001',
                'stock' => 20,
                'unit' => 'chiếc',
                'is_hot' => true,
                'order' => 3,
                'status' => 'active',
                'category_id' => $catProducts[2]->id,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Tạo ảnh cho sản phẩm
            ProductImage::create([
                'product_id' => $product->id,
                'image_link' => '/images/products/' . $product->slug . '-1.jpg',
                'alt_text' => $product->name,
                'is_main' => true,
                'order' => 1,
                'status' => 'active',
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'image_link' => '/images/products/' . $product->slug . '-2.jpg',
                'alt_text' => $product->name . ' - Ảnh 2',
                'is_main' => false,
                'order' => 2,
                'status' => 'active',
            ]);
        }
    }

    private function createPosts($catPosts)
    {
        $posts = [
            [
                'title' => 'Chào mừng đến với Vũ Phúc Bakery',
                'content' => '<p>Vũ Phúc Bakery tự hào là một trong những tiệm bánh hàng đầu với nhiều năm kinh nghiệm trong ngành. Chúng tôi cam kết mang đến cho khách hàng những sản phẩm bánh tươi ngon nhất.</p><p>Với đội ngũ thợ làm bánh chuyên nghiệp và nguyên liệu chất lượng cao, mỗi chiếc bánh tại Vũ Phúc đều được chế biến tỉ mỉ và đầy tâm huyết.</p>',
                'seo_title' => 'Chào mừng đến với Vũ Phúc Bakery - Tiệm bánh uy tín',
                'seo_description' => 'Vũ Phúc Bakery - tiệm bánh uy tín với nhiều năm kinh nghiệm, chuyên cung cấp các loại bánh tươi ngon chất lượng cao.',
                'slug' => 'chao-mung-den-voi-vu-phuc-bakery',
                'thumbnail' => '/images/posts/chao-mung.jpg',
                'is_featured' => true,
                'type' => 'news',
                'order' => 1,
                'status' => 'active',
            ],
            [
                'title' => 'Hướng dẫn làm bánh mì tại nhà',
                'content' => '<p>Bánh mì là món ăn quen thuộc của người Việt. Hôm nay chúng tôi sẽ hướng dẫn bạn cách làm bánh mì thơm ngon tại nhà.</p><h3>Nguyên liệu cần chuẩn bị:</h3><ul><li>Bột mì: 500g</li><li>Men nướng: 7g</li><li>Muối: 10g</li><li>Đường: 12g</li><li>Nước: 320ml</li></ul><h3>Cách làm:</h3><p>Trộn tất cả nguyên liệu khô, sau đó thêm nước từ từ và nhào bột trong 10-15 phút...</p>',
                'seo_title' => 'Hướng dẫn làm bánh mì tại nhà đơn giản',
                'seo_description' => 'Học cách làm bánh mì tại nhà với công thức đơn giản, dễ thực hiện từ Vũ Phúc Bakery.',
                'slug' => 'huong-dan-lam-banh-mi-tai-nha',
                'thumbnail' => '/images/posts/huong-dan-banh-mi.jpg',
                'is_featured' => false,
                'type' => 'normal',
                'order' => 2,
                'status' => 'active',
            ],
        ];

        foreach ($posts as $index => $postData) {
            $post = Post::create($postData);

            // Attach category dựa trên type
            $categoryToAttach = null;
            foreach ($catPosts as $catPost) {
                if ($catPost->type === $post->type) {
                    $categoryToAttach = $catPost;
                    break;
                }
            }

            if ($categoryToAttach) {
                $post->categories()->attach($categoryToAttach->id);
            }

            // Tạo ảnh cho bài viết
            PostImage::create([
                'post_id' => $post->id,
                'image_link' => '/images/posts/' . $post->slug . '-1.jpg',
                'alt_text' => $post->title,
                'order' => 1,
                'status' => 'active',
            ]);
        }
    }

    private function createCustomers()
    {
        Customer::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'nguyenvana@example.com',
            'password' => 'password',
            'phone' => '0901234567',
            'address' => '123 Đường ABC, Quận 1, TP.HCM',
            'status' => 'active',
        ]);

        Customer::create([
            'name' => 'Trần Thị B',
            'email' => 'tranthib@example.com',
            'password' => 'password',
            'phone' => '0907654321',
            'address' => '456 Đường XYZ, Quận 3, TP.HCM',
            'status' => 'active',
        ]);
    }

    private function createPartners()
    {
        $partners = [
            [
                'name' => 'Rich Products Vietnam',
                'logo_link' => '/images/partners/rich-products.jpg',
                'website_link' => 'https://richproducts.com',
                'description' => 'Nhà cung cấp nguyên liệu làm bánh hàng đầu thế giới',
                'order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'KitchenAid',
                'logo_link' => '/images/partners/kitchenaid.jpg',
                'website_link' => 'https://kitchenaid.com',
                'description' => 'Thương hiệu thiết bị nhà bếp chuyên nghiệp',
                'order' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Anchor',
                'logo_link' => '/images/partners/anchor.jpg',
                'website_link' => 'https://anchor.com',
                'description' => 'Sản phẩm sữa và kem tươi chất lượng cao',
                'order' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Unox',
                'logo_link' => '/images/partners/unox.jpg',
                'website_link' => 'https://unox.com',
                'description' => 'Lò nướng và thiết bị làm bánh chuyên nghiệp',
                'order' => 4,
                'status' => 'active',
            ],
            [
                'name' => 'Callebaut',
                'logo_link' => '/images/partners/callebaut.jpg',
                'website_link' => 'https://callebaut.com',
                'description' => 'Chocolate và cocoa cao cấp từ Bỉ',
                'order' => 5,
                'status' => 'active',
            ],
            [
                'name' => 'Valrhona',
                'logo_link' => '/images/partners/valrhona.jpg',
                'website_link' => 'https://valrhona.com',
                'description' => 'Chocolate hảo hạng từ Pháp',
                'order' => 6,
                'status' => 'active',
            ],
            [
                'name' => 'Puratos',
                'logo_link' => '/images/partners/puratos.jpg',
                'website_link' => 'https://puratos.com',
                'description' => 'Nguyên liệu và giải pháp làm bánh toàn diện',
                'order' => 7,
                'status' => 'active',
            ],
            [
                'name' => 'Lesaffre',
                'logo_link' => '/images/partners/lesaffre.jpg',
                'website_link' => 'https://lesaffre.com',
                'description' => 'Men nướng và nguyên liệu lên men chuyên nghiệp',
                'order' => 8,
                'status' => 'active',
            ],
        ];

        foreach ($partners as $partnerData) {
            Partner::create($partnerData);
        }
    }

    private function createAssociations()
    {
        Association::create([
            'name' => 'Hiệp hội Bánh kẹo Việt Nam',
            'image_link' => '/images/associations/banh-keo-vn.jpg',
            'description' => 'Thành viên của Hiệp hội Bánh kẹo Việt Nam',
            'website_link' => 'https://vietbakers.org',
            'order' => 1,
            'status' => 'active',
        ]);
    }

    private function createEmployees()
    {
        $employee = Employee::create([
            'name' => 'Nguyễn Văn Thợ',
            'image_link' => '/images/employees/nguyen-van-tho.jpg',
            'position' => 'Thợ làm bánh chính',
            'description' => 'Thợ làm bánh chính với hơn 10 năm kinh nghiệm trong ngành bánh kẹo. Chuyên về các loại bánh truyền thống và hiện đại, có kỹ năng trang trí bánh xuất sắc và luôn đảm bảo chất lượng sản phẩm cao nhất.',
            'phone' => '0912345678',
            'email' => 'tho@vuphucbaking.com',
            'qr_code' => 'EMP001',
            'order' => 1,
            'status' => 'active',
        ]);

        EmployeeImage::create([
            'employee_id' => $employee->id,
            'image_link' => '/images/employees/nguyen-van-tho-work.jpg',
            'alt_text' => 'Nguyễn Văn Thợ đang làm việc',
            'caption' => 'Thợ làm bánh chính đang chế biến sản phẩm',
            'order' => 1,
            'status' => 'active',
        ]);
    }

    private function createSliders()
    {
        Slider::create([
            'image_link' => '/images/sliders/slider-1.jpg',
            'title' => 'Bánh tươi ngon mỗi ngày',
            'description' => 'Chuyên cung cấp bánh tươi ngon chất lượng cao',
            'link' => '/san-pham',
            'alt_text' => 'Bánh tươi ngon tại Vũ Phúc Bakery',
            'order' => 1,
            'status' => 'active',
        ]);

        Slider::create([
            'image_link' => '/images/sliders/slider-2.jpg',
            'title' => 'Bánh kem sinh nhật đặc biệt',
            'description' => 'Đặt bánh kem sinh nhật theo yêu cầu với nhiều mẫu mã đẹp mắt',
            'link' => '/banh-kem',
            'alt_text' => 'Bánh kem sinh nhật đẹp mắt',
            'order' => 2,
            'status' => 'active',
        ]);
    }

    private function createMenuItems($catProducts, $catPosts)
    {
        // Menu chính
        MenuItem::create([
            'label' => 'Trang chủ',
            'type' => 'link',
            'link' => '/',
            'order' => 1,
            'status' => 'active',
        ]);

        MenuItem::create([
            'label' => 'Sản phẩm',
            'type' => 'cat_product',
            'cat_product_id' => $catProducts[0]->id,
            'order' => 2,
            'status' => 'active',
        ]);

        MenuItem::create([
            'label' => 'Tin tức',
            'type' => 'cat_post',
            'cat_post_id' => $catPosts[0]->id,
            'order' => 3,
            'status' => 'active',
        ]);

        MenuItem::create([
            'label' => 'Liên hệ',
            'type' => 'link',
            'link' => '/lien-he',
            'order' => 4,
            'status' => 'active',
        ]);
    }
}