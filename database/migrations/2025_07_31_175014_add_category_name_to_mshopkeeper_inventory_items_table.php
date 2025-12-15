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
            $table->string('category_name')->nullable()->after('category_mshopkeeper_id')->comment('Tên danh mục từ API');
            $table->index('category_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mshopkeeper_inventory_items', function (Blueprint $table) {
            $table->dropIndex(['category_name']);
            $table->dropColumn('category_name');
        });
    }
};
