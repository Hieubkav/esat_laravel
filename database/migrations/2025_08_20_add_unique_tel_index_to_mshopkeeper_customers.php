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
        // Xóa duplicate records trước khi thêm unique constraint
        $this->removeDuplicateRecords();
        
        Schema::table('mshopkeeper_customers', function (Blueprint $table) {
            // Thêm unique index cho tel để tránh race condition
            $table->unique('tel', 'unique_mshopkeeper_customers_tel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mshopkeeper_customers', function (Blueprint $table) {
            $table->dropUnique('unique_mshopkeeper_customers_tel');
        });
    }

    /**
     * Remove duplicate records based on tel field
     */
    private function removeDuplicateRecords(): void
    {
        // Tìm và xóa các bản ghi duplicate, giữ lại bản ghi có id nhỏ nhất
        DB::statement("
            DELETE c1 FROM mshopkeeper_customers c1
            INNER JOIN mshopkeeper_customers c2 
            WHERE c1.id > c2.id 
            AND c1.tel = c2.tel 
            AND c1.tel IS NOT NULL
        ");
    }
};
