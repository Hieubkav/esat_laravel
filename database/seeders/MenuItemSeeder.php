<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        MenuItem::truncate();

        // Tạo menu cấp 1: Giới thiệu
        $gioiThieu = MenuItem::create([
            'label' => 'Giới thiệu',
            'type' => 'link',
            'link' => '#',
            'order' => 1,
            'status' => 'active'
        ]);

        // Tạo menu cấp 2 cho Giới thiệu
        MenuItem::create([
            'parent_id' => $gioiThieu->id,
            'label' => 'Vũ Phúc Baking',
            'type' => 'link',
            'link' => '/gioi-thieu/vu-phuc-baking',
            'order' => 1,
            'status' => 'active'
        ]);

        MenuItem::create([
            'parent_id' => $gioiThieu->id,
            'label' => 'VBA Academy',
            'type' => 'link',
            'link' => '/gioi-thieu/vba-academy',
            'order' => 2,
            'status' => 'active'
        ]);

        // Tạo menu cấp 1: Sản phẩm
        $sanPham = MenuItem::create([
            'label' => 'Sản phẩm',
            'type' => 'link',
            'link' => '/san-pham',
            'order' => 2,
            'status' => 'active'
        ]);

        // Tạo menu cấp 2 cho Sản phẩm
        MenuItem::create([
            'parent_id' => $sanPham->id,
            'label' => 'Bánh ngọt',
            'type' => 'link',
            'link' => '/san-pham/banh-ngot',
            'order' => 1,
            'status' => 'active'
        ]);

        MenuItem::create([
            'parent_id' => $sanPham->id,
            'label' => 'Bánh mì',
            'type' => 'link',
            'link' => '/san-pham/banh-mi',
            'order' => 2,
            'status' => 'active'
        ]);

        MenuItem::create([
            'parent_id' => $sanPham->id,
            'label' => 'Bánh kem',
            'type' => 'link',
            'link' => '/san-pham/banh-kem',
            'order' => 3,
            'status' => 'active'
        ]);

        // Tạo menu cấp 1: Khóa học
        MenuItem::create([
            'label' => 'Khóa học',
            'type' => 'link',
            'link' => '/khoa-hoc',
            'order' => 3,
            'status' => 'active'
        ]);

        // Tạo menu cấp 1: Tin tức
        $tinTuc = MenuItem::create([
            'label' => 'Tin tức',
            'type' => 'link',
            'link' => '/tin-tuc',
            'order' => 4,
            'status' => 'active'
        ]);

        // Tạo menu cấp 2 cho Tin tức
        MenuItem::create([
            'parent_id' => $tinTuc->id,
            'label' => 'Tin công ty',
            'type' => 'link',
            'link' => '/tin-tuc/tin-cong-ty',
            'order' => 1,
            'status' => 'active'
        ]);

        MenuItem::create([
            'parent_id' => $tinTuc->id,
            'label' => 'Kiến thức làm bánh',
            'type' => 'link',
            'link' => '/tin-tuc/kien-thuc-lam-banh',
            'order' => 2,
            'status' => 'active'
        ]);

        // Tạo menu cấp 1: Liên hệ
        MenuItem::create([
            'label' => 'Liên hệ',
            'type' => 'link',
            'link' => '/lien-he',
            'order' => 5,
            'status' => 'active'
        ]);

        echo "✅ Đã tạo " . MenuItem::count() . " menu items\n";
        echo "📋 Cấu trúc menu:\n";
        
        $parentMenus = MenuItem::whereNull('parent_id')->orderBy('order')->get();
        foreach ($parentMenus as $parent) {
            echo "  🔸 {$parent->label}\n";
            foreach ($parent->children as $child) {
                echo "    └── {$child->label}\n";
            }
        }
    }
}
