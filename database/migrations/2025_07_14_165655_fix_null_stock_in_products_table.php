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
        // Cập nhật tất cả các sản phẩm có stock NULL thành 0
        DB::table('products')
            ->whereNull('stock')
            ->update(['stock' => 0]);

        // Đảm bảo trường stock không được phép NULL
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback vì đây là fix dữ liệu
        // Schema::table('products', function (Blueprint $table) {
        //     $table->integer('stock')->nullable()->change();
        // });
    }
};
