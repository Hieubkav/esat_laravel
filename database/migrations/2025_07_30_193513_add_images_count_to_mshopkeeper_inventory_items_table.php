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
        Schema::table('mshopkeeper_inventory_items', function (Blueprint $table) {
            $table->integer('images_count')->default(0)->after('picture')->comment('Số lượng ảnh từ ListPictureUrl');
            $table->index('images_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mshopkeeper_inventory_items', function (Blueprint $table) {
            $table->dropIndex(['images_count']);
            $table->dropColumn('images_count');
        });
    }
};
