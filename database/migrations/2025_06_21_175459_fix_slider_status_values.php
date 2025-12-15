<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật các giá trị status từ boolean/integer sang string
        DB::table('sliders')->where('status', '1')->orWhere('status', 1)->orWhere('status', true)->update(['status' => 'active']);
        DB::table('sliders')->where('status', '0')->orWhere('status', 0)->orWhere('status', false)->update(['status' => 'inactive']);

        // Đảm bảo tất cả giá trị null hoặc không hợp lệ đều thành 'active'
        DB::table('sliders')->whereNotIn('status', ['active', 'inactive'])->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback vì đây là fix data
    }
};
