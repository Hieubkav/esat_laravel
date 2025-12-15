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
        Schema::create('mshopkeeper_inventory_items', function (Blueprint $table) {
            $table->id();

            // MShopKeeper Inventory Item fields theo API response
            $table->string('mshopkeeper_id')->unique()->comment('ID từ MShopKeeper API');
            $table->string('code')->index()->comment('Mã hàng hóa');
            $table->string('name')->index()->comment('Tên hàng hóa');
            $table->integer('item_type')->default(1)->comment('1=Hàng Hoá, 2=Combo, 4=Dịch vụ');
            $table->string('barcode')->nullable()->index()->comment('Mã vạch');
            
            // Pricing information
            $table->decimal('selling_price', 15, 2)->default(0)->comment('Giá bán');
            $table->decimal('cost_price', 15, 2)->default(0)->comment('Giá mua');
            $table->decimal('avg_unit_price', 15, 2)->default(0)->comment('Giá bán trung bình');
            
            // Product attributes
            $table->string('color')->nullable()->comment('Màu sắc');
            $table->string('size')->nullable()->comment('Kích thước');
            $table->string('material')->nullable()->comment('Chất liệu');
            $table->text('description')->nullable()->comment('Mô tả');
            
            // Status and type
            $table->boolean('is_item')->default(false)->comment('true = hàng hóa bán được, false = mẫu mã cha');
            $table->boolean('inactive')->default(false)->comment('true = ngừng kinh doanh');
            
            // Unit information
            $table->string('unit_id')->nullable()->comment('ID đơn vị tính');
            $table->string('unit_name')->nullable()->comment('Tên đơn vị tính');
            
            // Media
            $table->string('picture')->nullable()->comment('Đường dẫn ảnh');
            
            // Hierarchy (for parent-child relationship)
            $table->string('parent_mshopkeeper_id')->nullable()->index()->comment('ID cha từ MShopKeeper');
            $table->unsignedBigInteger('parent_id')->nullable()->index()->comment('ID cha trong database local');
            
            // Category relationship
            $table->string('category_mshopkeeper_id')->nullable()->index()->comment('ID danh mục từ MShopKeeper');
            
            // Inventory summary (tổng hợp từ các chi nhánh)
            $table->integer('total_on_hand')->default(0)->comment('Tổng tồn kho');
            $table->integer('total_ordered')->default(0)->comment('Tổng đang đặt hàng');
            
            // Sync tracking
            $table->timestamp('last_synced_at')->nullable()->comment('Lần sync cuối từ API');
            $table->string('sync_status')->default('pending')->index()->comment('Trạng thái sync: pending, synced, error');
            $table->text('sync_error')->nullable()->comment('Lỗi sync nếu có');
            $table->json('raw_data')->nullable()->comment('Dữ liệu thô từ API');

            $table->timestamps();

            // Indexes for performance
            $table->index(['sync_status', 'last_synced_at']);
            $table->index(['item_type', 'inactive']);
            $table->index(['is_item', 'inactive']);
            $table->index(['selling_price', 'inactive']);
            $table->index(['total_on_hand', 'inactive']);
            $table->index(['parent_mshopkeeper_id', 'is_item']);
            
            // Foreign key constraint
            $table->foreign('parent_id')->references('id')->on('mshopkeeper_inventory_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mshopkeeper_inventory_items');
    }
};
