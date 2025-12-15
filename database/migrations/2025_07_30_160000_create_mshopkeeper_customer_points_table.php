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
        Schema::create('mshopkeeper_customer_points', function (Blueprint $table) {
            $table->id();

            // MShopKeeper Customer Point fields theo API response
            $table->string('original_id')->unique()->comment('OriginalId từ MShopKeeper API');
            $table->string('tel')->nullable()->index()->comment('Số điện thoại khách hàng');
            $table->string('full_name')->index()->comment('Tên đầy đủ khách hàng');
            $table->integer('total_point')->default(0)->index()->comment('Tổng điểm đã tích lũy');

            // Sync tracking
            $table->timestamp('last_synced_at')->nullable()->comment('Lần sync cuối từ API');
            $table->string('sync_status')->default('pending')->index()->comment('Trạng thái sync: pending, synced, error');
            $table->text('sync_error')->nullable()->comment('Lỗi sync nếu có');
            $table->json('raw_data')->nullable()->comment('Dữ liệu thô từ API');

            $table->timestamps();

            // Indexes for performance
            $table->index(['sync_status', 'last_synced_at']);
            $table->index(['total_point', 'full_name']);
            $table->index(['tel', 'total_point']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mshopkeeper_customer_points');
    }
};
