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
        // Sử dụng raw SQL để cập nhật enum
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM('link', 'cat_post', 'all_posts', 'post', 'cat_product', 'all_products', 'product', 'display_only') NOT NULL DEFAULT 'link'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback về enum cũ
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM('link', 'cat_post', 'post', 'cat_product', 'product', 'display_only') NOT NULL DEFAULT 'link'");
    }
};
