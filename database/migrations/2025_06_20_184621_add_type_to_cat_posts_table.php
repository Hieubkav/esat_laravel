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
        Schema::table('cat_posts', function (Blueprint $table) {
            // Thêm trường type giống như trong bảng posts
            $table->enum('type', ['normal', 'news', 'service', 'course'])
                  ->default('normal')
                  ->after('status')
                  ->comment('Loại chuyên mục: normal, news, service, course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_posts', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
