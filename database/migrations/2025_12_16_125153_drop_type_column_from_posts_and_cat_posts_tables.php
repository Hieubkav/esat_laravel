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
        // Drop type column from posts table
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'type')) {
                $table->dropColumn('type');
            }
        });

        // Drop type column from cat_posts table
        Schema::table('cat_posts', function (Blueprint $table) {
            if (Schema::hasColumn('cat_posts', 'type')) {
                $table->dropColumn('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('type')->default('normal')->after('is_featured');
        });

        Schema::table('cat_posts', function (Blueprint $table) {
            $table->string('type')->default('normal')->after('status');
        });
    }
};
