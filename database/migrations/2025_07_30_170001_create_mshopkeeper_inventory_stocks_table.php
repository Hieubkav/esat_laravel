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
        Schema::create('mshopkeeper_inventory_stocks', function (Blueprint $table) {
            $table->id();

            // Relationship với inventory item
            $table->unsignedBigInteger('inventory_item_id')->comment('ID inventory item trong database local');
            $table->string('product_mshopkeeper_id')->index()->comment('Product ID từ MShopKeeper');
            $table->string('product_code')->index()->comment('Mã sản phẩm');
            $table->string('product_name')->comment('Tên sản phẩm');
            
            // Branch information
            $table->string('branch_mshopkeeper_id')->index()->comment('Branch ID từ MShopKeeper');
            $table->string('branch_name')->comment('Tên chi nhánh');
            
            // Stock information
            $table->integer('on_hand')->default(0)->comment('Số lượng tồn kho');
            $table->integer('ordered')->default(0)->comment('Số lượng đang đặt hàng');
            $table->decimal('selling_price', 15, 2)->default(0)->comment('Giá bán tại chi nhánh');
            
            // Sync tracking
            $table->timestamp('last_synced_at')->nullable()->comment('Lần sync cuối từ API');
            $table->string('sync_status')->default('pending')->index()->comment('Trạng thái sync: pending, synced, error');
            $table->text('sync_error')->nullable()->comment('Lỗi sync nếu có');
            $table->json('raw_data')->nullable()->comment('Dữ liệu thô từ API');

            $table->timestamps();

            // Indexes for performance
            $table->index(['inventory_item_id', 'branch_mshopkeeper_id'], 'idx_inventory_item_branch');
            $table->index(['product_mshopkeeper_id', 'branch_mshopkeeper_id'], 'idx_product_branch');
            $table->index(['on_hand', 'sync_status'], 'idx_stock_sync');
            $table->index(['sync_status', 'last_synced_at'], 'idx_sync_status_time');
            
            // Foreign key constraint
            $table->foreign('inventory_item_id')->references('id')->on('mshopkeeper_inventory_items')->onDelete('cascade');
            
            // Unique constraint để tránh duplicate
            $table->unique(['product_mshopkeeper_id', 'branch_mshopkeeper_id'], 'unique_product_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mshopkeeper_inventory_stocks');
    }
};
