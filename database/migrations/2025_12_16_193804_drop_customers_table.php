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
        // Drop foreign keys first (if tables exist)
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'customer_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            });
        }

        if (Schema::hasTable('carts') && Schema::hasColumn('carts', 'user_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        // Drop customers table
        Schema::dropIfExists('customers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate customers table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Re-add foreign keys
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->nullOnDelete();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('customers')->nullOnDelete();
        });
    }
};
