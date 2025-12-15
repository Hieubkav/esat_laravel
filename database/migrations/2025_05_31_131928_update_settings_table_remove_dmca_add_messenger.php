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
        Schema::table('settings', function (Blueprint $table) {
            // Bỏ cột dmca_link
            $table->dropColumn('dmca_link');

            // Thêm cột messenger_link sau tiktok_link
            $table->string('messenger_link')->nullable()->after('tiktok_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Thêm lại cột dmca_link
            $table->string('dmca_link')->nullable()->after('working_hours');

            // Bỏ cột messenger_link
            $table->dropColumn('messenger_link');
        });
    }
};
