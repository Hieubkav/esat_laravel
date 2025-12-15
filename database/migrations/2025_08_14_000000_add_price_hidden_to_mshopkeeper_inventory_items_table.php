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
            $table->boolean('price_hidden')->default(false)->after('is_featured')->comment('Ẩn giá sản phẩm trên web');
            $table->index('price_hidden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mshopkeeper_inventory_items', function (Blueprint $table) {
            $table->dropIndex(['price_hidden']);
            $table->dropColumn('price_hidden');
        });
    }
};
