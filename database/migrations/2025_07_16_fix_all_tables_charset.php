<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Danh sách các bảng cần sửa charset
        $tables = [
            'users', 'posts', 'products', 'post_categories', 'product_categories',
            'menu_items', 'sliders', 'settings', 'partners', 'associations',
            'employees', 'orders', 'web_designs'
        ];

        foreach ($tables as $table) {
            // Kiểm tra bảng có tồn tại không
            if (Schema::hasTable($table)) {
                try {
                    // Sửa charset cho toàn bộ bảng
                    DB::statement("ALTER TABLE {$table} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci");
                    
                    // Sửa charset cho các cột text/varchar cụ thể
                    $this->fixTableColumns($table);
                    
                } catch (Exception $e) {
                    // Log lỗi nhưng không dừng migration
                    \Log::warning("Không thể sửa charset cho bảng {$table}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Sửa charset cho các cột cụ thể của từng bảng
     */
    private function fixTableColumns($table)
    {
        $columnMappings = [
            'posts' => ['title', 'slug', 'excerpt', 'content', 'meta_title', 'meta_description'],
            'products' => ['name', 'slug', 'description', 'short_description', 'meta_title', 'meta_description'],
            'post_categories' => ['name', 'slug', 'description'],
            'product_categories' => ['name', 'slug', 'description'],
            'menu_items' => ['title', 'url'],
            'sliders' => ['title', 'description', 'button_text', 'button_url'],
            'settings' => ['site_name', 'site_description', 'contact_address', 'contact_phone', 'contact_email'],
            'partners' => ['name', 'description'],
            'associations' => ['name', 'description'],
            'employees' => ['name', 'slug', 'position', 'description', 'phone', 'email'],
            'orders' => ['shipping_address', 'shipping_name', 'shipping_phone', 'shipping_email', 'note'],
            'users' => ['name', 'email'],
            'web_designs' => ['hero_title', 'hero_subtitle', 'about_title', 'about_content']
        ];

        if (isset($columnMappings[$table])) {
            foreach ($columnMappings[$table] as $column) {
                try {
                    if (Schema::hasColumn($table, $column)) {
                        // Xác định kiểu dữ liệu phù hợp
                        $columnType = $this->getColumnType($table, $column);
                        DB::statement("ALTER TABLE {$table} MODIFY COLUMN {$column} {$columnType} CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci");
                    }
                } catch (Exception $e) {
                    \Log::warning("Không thể sửa cột {$column} trong bảng {$table}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Xác định kiểu dữ liệu cho cột
     */
    private function getColumnType($table, $column)
    {
        // Các cột thường là TEXT
        $textColumns = ['content', 'description', 'short_description', 'about_content', 'shipping_address', 'note'];
        
        if (in_array($column, $textColumns)) {
            return 'TEXT';
        }
        
        // Mặc định là VARCHAR(255)
        return 'VARCHAR(255)';
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback vì đây là fix lỗi encoding
        // Rollback có thể gây mất dữ liệu tiếng Việt
    }
};
