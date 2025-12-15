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
        Schema::table('orders', function (Blueprint $table) {
            // Thêm các trường thông tin giao hàng
            $table->string('shipping_name')->nullable()->after('shipping_address');
            $table->string('shipping_phone', 20)->nullable()->after('shipping_name');
            $table->string('shipping_email')->nullable()->after('shipping_phone');

            // Thêm trường trạng thái thanh toán
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])
                  ->default('pending')->after('payment_method');

            // Cập nhật enum status (drop và tạo lại)
            $table->dropColumn('status');
        });

        // Thêm lại cột status với enum mới
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipping',
                'delivered',
                'cancelled',
                'refunded'
            ])->default('pending')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_name',
                'shipping_phone',
                'shipping_email',
                'payment_status',
                'status'
            ]);
        });

        // Khôi phục cột status với enum cũ
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])
                  ->default('pending')->after('total');
        });
    }
};
