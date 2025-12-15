<?php

namespace Database\Seeders;

use App\Models\WebDesign;
use Illuminate\Database\Seeder;

class WebDesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        WebDesign::truncate();

        // Tạo cấu hình mặc định cho các components
        $components = WebDesign::getDefaultComponents();

        foreach ($components as $key => $config) {
            WebDesign::create([
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
