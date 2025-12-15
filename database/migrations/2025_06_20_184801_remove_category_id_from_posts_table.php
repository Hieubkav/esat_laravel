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
        Schema::table('posts', function (Blueprint $table) {
            // Xóa foreign key constraint trước
            $table->dropForeign(['category_id']);
            // Sau đó xóa column
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Thêm lại column category_id
            $table->foreignId('category_id')->nullable()->after('status')->constrained('cat_posts');
        });
    }
};
