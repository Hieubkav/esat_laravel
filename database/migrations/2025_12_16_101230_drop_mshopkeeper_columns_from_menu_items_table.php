<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['mshopkeeper_inventory_item_id', 'mshopkeeper_category_id']);
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('mshopkeeper_inventory_item_id')->nullable();
            $table->string('mshopkeeper_category_id')->nullable();
        });
    }
};
