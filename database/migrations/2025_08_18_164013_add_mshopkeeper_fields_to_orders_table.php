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
        Schema::table('orders', function (Blueprint $table) {
            // MShopKeeper integration fields
            $table->string('mshopkeeper_order_id')->nullable()->after('note')->comment('OrderId từ MShopKeeper API');
            $table->string('mshopkeeper_order_no')->nullable()->after('mshopkeeper_order_id')->comment('OrderNo từ MShopKeeper API');
            $table->unsignedBigInteger('mshopkeeper_customer_id')->nullable()->after('mshopkeeper_order_no')->comment('ID của MShopKeeper customer');

            // Indexes
            $table->index('mshopkeeper_order_id');
            $table->index('mshopkeeper_order_no');
            $table->index('mshopkeeper_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['orders_mshopkeeper_order_id_index']);
            $table->dropIndex(['orders_mshopkeeper_order_no_index']);
            $table->dropIndex(['orders_mshopkeeper_customer_id_index']);

            $table->dropColumn([
                'mshopkeeper_order_id',
                'mshopkeeper_order_no',
                'mshopkeeper_customer_id'
            ]);
        });
    }
};
