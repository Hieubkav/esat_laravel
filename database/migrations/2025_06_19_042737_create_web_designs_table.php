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
        Schema::create('web_designs', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique()->comment('Khóa định danh section (hero-banner, about-us, etc.)');
            $table->boolean('is_visible')->default(true)->comment('Có hiển thị section này không');
            $table->json('settings')->nullable()->comment('Cấu hình chi tiết cho section (JSON)');
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->boolean('status')->default(true)->comment('Trạng thái hoạt động');
            $table->timestamps();

            $table->index(['section_key', 'status']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_designs');
    }
};
