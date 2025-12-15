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
            // Thêm trường created_by để lưu ID người tạo bài viết
            $table->foreignId('created_by')->nullable()->after('status')->constrained('users')->nullOnDelete();

            // Thêm trường updated_by để lưu ID người chỉnh sửa bài viết lần cuối
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Xóa các trường đã thêm
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['updated_by', 'created_by']);
        });
    }
};
