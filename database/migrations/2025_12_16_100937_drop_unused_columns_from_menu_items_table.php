<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Xóa foreign key constraints trước
            $table->dropForeign(['cat_post_id']);
            $table->dropForeign(['post_id']);
            $table->dropForeign(['cat_product_id']);
            $table->dropForeign(['product_id']);
            
            // Sau đó xóa các cột
            $table->dropColumn(['type', 'cat_post_id', 'post_id', 'cat_product_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('type')->default('link')->after('label');
            $table->unsignedBigInteger('cat_post_id')->nullable()->after('link');
            $table->unsignedBigInteger('post_id')->nullable()->after('cat_post_id');
            $table->unsignedBigInteger('cat_product_id')->nullable()->after('post_id');
            $table->unsignedBigInteger('product_id')->nullable()->after('cat_product_id');
        });
    }
};
