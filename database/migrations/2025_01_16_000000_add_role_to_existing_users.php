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
        // Kiểm tra xem cột role đã tồn tại chưa
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'post_manager'])->default('admin')->after('status');
            });
        }

        // Cập nhật role cho user hiện có
        // User đầu tiên (admin@vuphucbaking.com) sẽ là admin
        // User thứ hai (editor@vuphucbaking.com) sẽ là post_manager
        DB::table('users')
            ->where('email', 'admin@vuphucbaking.com')
            ->update(['role' => 'admin']);

        DB::table('users')
            ->where('email', 'editor@vuphucbaking.com')
            ->update(['role' => 'post_manager']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
