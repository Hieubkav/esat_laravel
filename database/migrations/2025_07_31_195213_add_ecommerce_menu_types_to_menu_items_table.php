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
        // Thêm các loại menu mới cho thương mại điện tử (chỉ giữ hàng hóa và danh mục MShopKeeper)
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback về enum cũ
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM(
            'link',
            'cat_post',
            'all_posts',
            'post',
            'cat_product',
            'all_products',
            'product',
            'display_only'
        ) NOT NULL DEFAULT 'link'");
    }
};
