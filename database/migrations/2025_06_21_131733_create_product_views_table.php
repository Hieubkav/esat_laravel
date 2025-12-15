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
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('ip_address', 45); // Hỗ trợ cả IPv4 và IPv6
            $table->string('session_id')->nullable();
            $table->timestamp('viewed_at');
            $table->timestamps();

            // Index để tối ưu query
            $table->index(['product_id', 'ip_address', 'viewed_at']);
            $table->index(['product_id', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_views');
    }
};
