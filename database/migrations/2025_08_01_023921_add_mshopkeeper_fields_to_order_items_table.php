<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Thêm fields cho MShopKeeper products
            $table->unsignedBigInteger('mshopkeeper_product_id')->nullable()->after('product_id')->comment('ID sản phẩm MShopKeeper');
            $table->string('mshopkeeper_product_code')->nullable()->after('mshopkeeper_product_id')->comment('Mã sản phẩm MShopKeeper');
            $table->string('mshopkeeper_product_name')->nullable()->after('mshopkeeper_product_code')->comment('Tên sản phẩm MShopKeeper');

            // Cho phép product_id nullable để hỗ trợ cả products cũ và MShopKeeper
            $table->unsignedBigInteger('product_id')->nullable()->change();

            // Thêm foreign key cho MShopKeeper products
            $table->foreign('mshopkeeper_product_id')->references('id')->on('mshopkeeper_inventory_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Xóa foreign key trước
            $table->dropForeign(['mshopkeeper_product_id']);

            // Xóa các columns đã thêm
            $table->dropColumn([
                'mshopkeeper_product_id',
                'mshopkeeper_product_code',
                'mshopkeeper_product_name'
            ]);

            // Khôi phục product_id về not null
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};
