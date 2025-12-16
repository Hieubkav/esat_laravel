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
        // Chuyển cột type từ ENUM sang VARCHAR để linh hoạt hơn
        // Giữ nguyên dữ liệu hiện tại
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'link'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback về ENUM (chỉ khi dữ liệu hợp lệ với ENUM)
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM('link', 'cat_post', 'all_posts', 'post', 'cat_product', 'all_products', 'product', 'display_only') NOT NULL DEFAULT 'link'");
    }
};
