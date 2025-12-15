<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;

class RestoreDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo Settings
        $this->createSettings();

        // 2. Tạo Categories
        $catProducts = $this->createCatProducts();
        $catPosts = $this->createCatPosts();

        // 3. Tạo Products và ProductImages
        $this->createProducts($catProducts);

        // 4. Tạo Posts và PostImages
        $this->createPosts($catPosts);

        // 5. Tạo Customers
        $this->createCustomers();

        // 6. Tạo Partners
        $this->createPartners();

        // 7. Tạo Associations
        $this->createAssociations();

        // 8. Tạo Employees
        $this->createEmployees();

        // 9. Tạo Sliders
        $this->createSliders();
    }

    private function createSettings()
    {
        Setting::firstOrCreate(['id' => 1], [
            'site_name' => 'Vũ Phúc Bakery',
            'logo_link' => 'settings/01JTV12EH63SST41MNKY4VZ2K6.png',
            'favicon_link' => 'settings/01JTV0Y60B0Y2X69S3C5RGXA20.png',
            'seo_title' => 'Vũ Phúc Baking - Nhà phân phối nguyên liệu ngành bánh và pha chế tại ĐBSCL',
            'seo_description' => 'Vũ Phúc Baking chuyên phân phối nguyên liệu ngành bánh và pha chế chất lượng cao tại khu vực Đồng bằng sông Cửu Long.',
            'og_image_link' => 'settings/01JTT4Y1MME0JHPWW12MNTS1ZY.jpg',
            'hotline' => '1900636340',
            'address' => '123 Đường ABC, Quận XYZ, TP.HCM',
            'email' => 'info@vuphucbaking.com',
            'slogan' => 'Nguyên liệu chất lượng - Thành công bền vững',
            'facebook_link' => 'https://facebook.com/vuphucbaking',
            'zalo_link' => 'https://zalo.me/vuphucbaking',
            'youtube_link' => 'https://youtube.com/vuphucbaking',
            'tiktok_link' => 'https://tiktok.com/@vuphucbaking',
            'working_hours' => '6:00 - 22:00 (Thứ 2 - Chủ nhật)',
            'order' => 1,
            'status' => 'active',
        ]);
    }

    private function createCatProducts()
    {
        $categories = [
            [
                'name' => 'Nguyên liệu làm bánh',
                'slug' => 'nguyen-lieu-lam-banh',
                'seo_title' => 'Nguyên liệu làm bánh chất lượng cao - Vũ Phúc Baking',
                'seo_description' => 'Cung cấp nguyên liệu làm bánh chất lượng cao từ các thương hiệu uy tín trên thế giới.',
                'image' => 'product-categories/8emmnn9GMwoOCVcxlLov.webp',
                'description' => 'Nguyên liệu làm bánh chất lượng cao từ các thương hiệu uy tín',
                'order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Thiết bị làm bánh',
                'slug' => 'thiet-bi-lam-banh',
                'seo_title' => 'Thiết bị làm bánh chuyên nghiệp - Vũ Phúc Baking',
                'seo_description' => 'Thiết bị làm bánh chuyên nghiệp cho tiệm bánh và nhà hàng.',
                'image' => '/images/categories/thiet-bi.jpg',
                'description' => 'Thiết bị làm bánh chuyên nghiệp',
                'order' => 2,
                'status' => 'active',
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $createdCategories[] = CatProduct::firstOrCreate(['slug' => $category['slug']], $category);
        }

        return $createdCategories;
    }

    private function createCatPosts()
    {
        $categories = [
            [
                'name' => 'Tin Tức',
                'slug' => 'tin-tuc',
                'seo_title' => 'Tin Tức - Vũ Phúc Baking',
                'seo_description' => 'Cập nhật tin tức mới nhất về sản phẩm và hoạt động của Vũ Phúc Baking.',
                'image' => '/images/categories/tin-tuc.jpg',
                'description' => 'Tin tức và cập nhật mới nhất',
                'order' => 1,
                'status' => 'active',
                'type' => 'news',
            ],
            [
                'name' => 'Hướng Dẫn',
                'slug' => 'huong-dan',
                'seo_title' => 'Hướng Dẫn - Vũ Phúc Baking',
                'seo_description' => 'Hướng dẫn sử dụng sản phẩm và kỹ thuật làm bánh.',
                'image' => '/images/categories/huong-dan.jpg',
                'description' => 'Hướng dẫn sử dụng sản phẩm',
                'order' => 2,
                'status' => 'active',
                'type' => 'normal',
            ],
            [
                'name' => 'Dịch vụ',
                'slug' => 'dich-vu',
                'seo_title' => 'Dịch vụ - Vũ Phúc Baking',
                'seo_description' => 'Các dịch vụ chuyên nghiệp của Vũ Phúc Baking.',
                'image' => '/images/categories/dich-vu.jpg',
                'description' => 'Dịch vụ chuyên nghiệp',
                'order' => 3,
                'status' => 'active',
                'type' => 'service',
            ],
            [
                'name' => 'Khóa học',
                'slug' => 'khoa-hoc',
                'seo_title' => 'Khóa học - Vũ Phúc Baking Academy',
                'seo_description' => 'Khóa học làm bánh chuyên nghiệp tại Vũ Phúc Baking Academy.',
                'image' => '/images/categories/khoa-hoc.jpg',
                'description' => 'Khóa học làm bánh chuyên nghiệp',
                'order' => 4,
                'status' => 'active',
                'type' => 'course',
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $createdCategories[] = CatPost::firstOrCreate(['slug' => $category['slug']], $category);
        }

        return $createdCategories;
    }

    private function createProducts($catProducts)
    {
        $products = [
            [
                'name' => 'Bột mì số 8 - Rich Products',
                'description' => 'Bột mì số 8 chất lượng cao từ Rich Products Vietnam, phù hợp cho làm bánh mì và bánh ngọt.',
                'seo_title' => 'Bột mì số 8 Rich Products - Vũ Phúc Baking',
                'seo_description' => 'Bột mì số 8 chất lượng cao từ Rich Products, phù hợp cho làm bánh mì và bánh ngọt chuyên nghiệp.',
                'slug' => 'bot-mi-so-8-rich-products',
                'price' => 45000,
                'compare_price' => 50000,
                'brand' => 'Rich Products',
                'sku' => 'RP001',
                'stock' => 100,
                'unit' => 'kg',
                'is_hot' => true,
                'order' => 1,
                'status' => 'active',
                'category_id' => $catProducts[0]->id,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(['slug' => $productData['slug']], $productData);

            // Tạo ảnh cho sản phẩm nếu chưa có
            if (!$product->productImages()->exists()) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_link' => 'products/hEsEnjAuAQg5nwV8Y9Sd.webp',
                    'alt_text' => $product->name,
                    'is_main' => true,
                    'order' => 1,
                    'status' => 'active',
                ]);
            }
        }
    }

    private function createPosts($catPosts)
    {
        // Posts sẽ được tạo bởi các seeder riêng biệt
        // PostNewsSeeder, PostServiceSeeder, PostCourseSeeder
    }

    private function createCustomers()
    {
        Customer::firstOrCreate(['email' => 'customer@example.com'], [
            'name' => 'Khách hàng mẫu',
            'password' => 'password',
            'phone' => '0901234567',
            'address' => '123 Đường ABC, Quận 1, TP.HCM',
            'status' => 'active',
        ]);
    }

    private function createPartners()
    {
        $partners = [
            [
                'name' => 'Rich Products Vietnam',
                'logo_link' => 'partners/01JTT720VY70GVMGPCC26TN0KS.jpg',
                'website_link' => 'https://richproducts.com',
                'description' => 'Nhà cung cấp nguyên liệu làm bánh hàng đầu thế giới',
                'order' => 1,
                'status' => 'active',
            ],
        ];

        foreach ($partners as $partnerData) {
            Partner::firstOrCreate(['name' => $partnerData['name']], $partnerData);
        }
    }

    private function createAssociations()
    {
        Association::firstOrCreate(['name' => 'Hiệp hội Bánh kẹo Việt Nam'], [
            'image_link' => 'associations/logos/hiep-hoi-banh-keo.jpg',
            'description' => 'Thành viên của Hiệp hội Bánh kẹo Việt Nam',
            'website_link' => 'https://vietbakers.org',
            'order' => 1,
            'status' => 'active',
        ]);
    }

    private function createEmployees()
    {
        $employee = Employee::firstOrCreate(['email' => 'tho@vuphucbaking.com'], [
            'name' => 'Nguyễn Văn Thợ',
            'image_link' => 'employees/avatars/nguyen-van-tho.jpg',
            'position' => 'Thợ làm bánh chính',
            'description' => 'Thợ làm bánh chính với hơn 10 năm kinh nghiệm trong ngành bánh kẹo.',
            'phone' => '0912345678',
            'qr_code' => 'EMP001',
            'order' => 1,
            'status' => 'active',
        ]);
    }

    private function createSliders()
    {
        Slider::firstOrCreate(['order' => 1], [
            'image_link' => 'carousel/01JTT6QDEN5A3D1TSDPFK0MTW1.jpg',
            'title' => 'Vũ Phúc Baking - Đối tác tin cậy',
            'description' => 'Chuyên phân phối nguyên liệu ngành bánh và pha chế chất lượng cao',
            'link' => '/san-pham',
            'alt_text' => 'Vũ Phúc Baking - Nguyên liệu chất lượng',
            'status' => 'active',
        ]);

        Slider::firstOrCreate(['order' => 2], [
            'image_link' => 'carousel/01JTT6QXX2TTAADPEN2FY85E3G.jpg',
            'title' => 'Thiết bị chuyên nghiệp',
            'description' => 'Cung cấp thiết bị làm bánh chuyên nghiệp cho tiệm bánh và nhà hàng',
            'link' => '/thiet-bi',
            'alt_text' => 'Thiết bị làm bánh chuyên nghiệp',
            'status' => 'active',
        ]);
    }
}
