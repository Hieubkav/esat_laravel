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
        // Kiểm tra và xóa dữ liệu không hợp lệ trước khi sửa constraint
        $this->cleanupInvalidData();
        
        Schema::table('mshopkeeper_carts', function (Blueprint $table) {
            // Drop foreign key constraint cũ
            $table->dropForeign(['customer_id']);
        });

        Schema::table('mshopkeeper_carts', function (Blueprint $table) {
            // Thêm foreign key constraint mới đến bảng mshopkeeper_customers
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('mshopkeeper_customers')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mshopkeeper_carts', function (Blueprint $table) {
            // Drop foreign key constraint mới
            $table->dropForeign(['customer_id']);
        });

        Schema::table('mshopkeeper_carts', function (Blueprint $table) {
            // Khôi phục foreign key constraint cũ (nếu cần)
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->nullOnDelete();
        });
    }

    /**
     * Xóa dữ liệu không hợp lệ trước khi sửa constraint
     */
    private function cleanupInvalidData(): void
    {
        // Tìm các cart có customer_id không tồn tại trong mshopkeeper_customers
        $invalidCarts = DB::table('mshopkeeper_carts')
            ->leftJoin('mshopkeeper_customers', 'mshopkeeper_carts.customer_id', '=', 'mshopkeeper_customers.id')
            ->whereNull('mshopkeeper_customers.id')
            ->whereNotNull('mshopkeeper_carts.customer_id')
            ->pluck('mshopkeeper_carts.id');

        if ($invalidCarts->isNotEmpty()) {
            // Log các cart sẽ bị xóa
            \Log::warning('Cleaning up invalid cart data', [
                'invalid_cart_ids' => $invalidCarts->toArray(),
                'count' => $invalidCarts->count()
            ]);

            // Xóa cart items trước
            DB::table('mshopkeeper_cart_items')
                ->whereIn('cart_id', $invalidCarts)
                ->delete();

            // Xóa carts không hợp lệ
            DB::table('mshopkeeper_carts')
                ->whereIn('id', $invalidCarts)
                ->delete();
        }
    }
};
