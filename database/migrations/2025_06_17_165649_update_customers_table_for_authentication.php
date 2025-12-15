<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Thêm remember_token cho authentication
            $table->rememberToken()->after('password');
        });

        // Thêm constraint để đảm bảo ít nhất email hoặc phone phải có giá trị
        DB::statement('ALTER TABLE customers ADD CONSTRAINT check_email_or_phone CHECK (email IS NOT NULL OR phone IS NOT NULL)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });

        // Xóa constraint
        DB::statement('ALTER TABLE customers DROP CONSTRAINT check_email_or_phone');
    }
};
