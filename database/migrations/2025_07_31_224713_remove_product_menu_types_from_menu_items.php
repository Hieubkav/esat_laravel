<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật enum để bỏ các product menu types và all_mshopkeeper_categories
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM(
            'link',
            'cat_post',
            'all_posts',
            'post',
            'display_only',
            'mshopkeeper_inventory',
            'all_mshopkeeper_inventory',
            'mshopkeeper_category'
        ) NOT NULL DEFAULT 'link'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback - thêm lại các enum types đã xóa
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM(
            'link',
            'cat_post',
            'all_posts',
            'post',
            'cat_product',
            'all_products',
            'product',
            'display_only',
            'mshopkeeper_inventory',
            'all_mshopkeeper_inventory',
            'mshopkeeper_category',
            'all_mshopkeeper_categories'
        ) NOT NULL DEFAULT 'link'");
    }
};
