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
        Schema::table('mshopkeeper_customers', function (Blueprint $table) {
            // Authentication fields for website login
            $table->string('password')->nullable()->after('member_level_name')->comment('Mật khẩu để đăng nhập website');
            $table->string('plain_password')->nullable()->after('password')->comment('Mật khẩu gốc cho admin xem');
            $table->rememberToken()->after('plain_password')->comment('Remember token cho authentication');
            $table->timestamp('email_verified_at')->nullable()->after('remember_token')->comment('Thời gian verify email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mshopkeeper_customers', function (Blueprint $table) {
            $table->dropColumn([
                'password',
                'plain_password',
                'remember_token',
                'email_verified_at'
            ]);
        });
    }
};
