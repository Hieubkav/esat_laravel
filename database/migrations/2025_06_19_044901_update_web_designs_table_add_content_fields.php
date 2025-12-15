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
        Schema::table('web_designs', function (Blueprint $table) {
            // Thêm các trường mới
            $table->string('component_name')->after('id')->comment('Tên component hiển thị');
            $table->string('title')->nullable()->after('section_key')->comment('Tiêu đề chính');
            $table->string('subtitle')->nullable()->after('title')->comment('Tiêu đề phụ');
            $table->json('content')->nullable()->after('subtitle')->comment('Nội dung JSON');
            $table->string('image_url')->nullable()->after('content')->comment('URL hình ảnh');
            $table->string('video_url')->nullable()->after('image_url')->comment('URL video');
            $table->string('button_text')->nullable()->after('video_url')->comment('Text nút bấm');
            $table->string('button_url')->nullable()->after('button_text')->comment('URL nút bấm');

            // Đổi tên cột
            $table->renameColumn('section_key', 'component_key');
            $table->renameColumn('is_visible', 'is_active');
            $table->renameColumn('order', 'position');

            // Xóa cột không cần
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_designs', function (Blueprint $table) {
            // Khôi phục tên cột
            $table->renameColumn('component_key', 'section_key');
            $table->renameColumn('is_active', 'is_visible');
            $table->renameColumn('position', 'order');

            // Xóa các trường đã thêm
            $table->dropColumn([
                'component_name', 'title', 'subtitle', 'content',
                'image_url', 'video_url', 'button_text', 'button_url'
            ]);

            // Thêm lại cột status
            $table->boolean('status')->default(true)->after('settings');
        });
    }
};
