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
        Schema::create('mshopkeeper_categories', function (Blueprint $table) {
            $table->id();

            // MShopKeeper fields
            $table->string('mshopkeeper_id')->unique()->comment('ID từ MShopKeeper API');
            $table->string('code')->nullable()->index()->comment('Mã danh mục');
            $table->string('name')->index()->comment('Tên danh mục');
            $table->text('description')->nullable()->comment('Mô tả danh mục');
            $table->integer('grade')->default(0)->index()->comment('Cấp độ trong cây danh mục');
            $table->boolean('inactive')->default(false)->index()->comment('Trạng thái không hoạt động');
            $table->boolean('is_leaf')->default(false)->index()->comment('Có phải node lá không');
            $table->string('parent_mshopkeeper_id')->nullable()->index()->comment('ID cha từ MShopKeeper');
            $table->integer('sort_order')->default(0)->index()->comment('Thứ tự sắp xếp');

            // Sync tracking
            $table->timestamp('last_synced_at')->nullable()->comment('Lần sync cuối từ API');
            $table->string('sync_status')->default('pending')->index()->comment('Trạng thái sync: pending, synced, error');
            $table->text('sync_error')->nullable()->comment('Lỗi sync nếu có');
            $table->json('raw_data')->nullable()->comment('Dữ liệu thô từ API');

            // Laravel relationship helper
            $table->unsignedBigInteger('parent_id')->nullable()->index()->comment('ID cha trong database');

            $table->timestamps();

            // Indexes for performance
            $table->index(['inactive', 'is_leaf']);
            $table->index(['grade', 'sort_order']);
            $table->index(['sync_status', 'last_synced_at']);

            // Foreign key constraint
            $table->foreign('parent_id')->references('id')->on('mshopkeeper_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mshopkeeper_categories');
    }
};
