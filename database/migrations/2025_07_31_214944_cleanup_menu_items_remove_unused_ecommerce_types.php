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
        // Xóa các columns không cần thiết đã được thêm trước đó
        if (Schema::hasColumn('menu_items', 'order_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('order_id');
            });
        }

        if (Schema::hasColumn('menu_items', 'customer_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('customer_id');
            });
        }

        if (Schema::hasColumn('menu_items', 'cart_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('cart_id');
            });
        }

        if (Schema::hasColumn('menu_items', 'mshopkeeper_customer_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('mshopkeeper_customer_id');
            });
        }

        if (Schema::hasColumn('menu_items', 'mshopkeeper_customer_point_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('mshopkeeper_customer_point_id');
            });
        }

        // Cập nhật enum để chỉ giữ lại các types cần thiết
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
        // Rollback - thêm lại các columns đã xóa
        Schema::table('menu_items', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('customer_id')->nullable()->after('order_id');
            $table->unsignedBigInteger('cart_id')->nullable()->after('customer_id');
            $table->unsignedBigInteger('mshopkeeper_customer_id')->nullable()->after('cart_id');
            $table->unsignedBigInteger('mshopkeeper_customer_point_id')->nullable()->after('mshopkeeper_category_id');
        });

        // Rollback enum
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM(
            'link',
            'cat_post',
            'all_posts',
            'post',
            'cat_product',
            'all_products',
            'product',
            'display_only',
            'order',
            'all_orders',
            'customer',
            'all_customers',
            'cart',
            'all_carts',
            'mshopkeeper_customer',
            'all_mshopkeeper_customers',
            'mshopkeeper_inventory',
            'all_mshopkeeper_inventory',
            'mshopkeeper_category',
            'all_mshopkeeper_categories',
            'mshopkeeper_customer_point',
            'all_mshopkeeper_customer_points'
        ) NOT NULL DEFAULT 'link'");
    }
};
