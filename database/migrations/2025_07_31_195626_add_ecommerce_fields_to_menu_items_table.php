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
        Schema::table('menu_items', function (Blueprint $table) {
            // Chỉ thêm các foreign key columns cần thiết cho MShopKeeper
            $table->unsignedBigInteger('mshopkeeper_inventory_item_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('mshopkeeper_category_id')->nullable()->after('mshopkeeper_inventory_item_id');

            // Thêm foreign key constraints (nếu cần)
            // $table->foreign('mshopkeeper_inventory_item_id')->references('id')->on('mshopkeeper_inventory_items')->onDelete('cascade');
            // $table->foreign('mshopkeeper_category_id')->references('id')->on('mshopkeeper_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Xóa các columns đã thêm
            $table->dropColumn([
                'mshopkeeper_inventory_item_id',
                'mshopkeeper_category_id'
            ]);
        });
    }
};
