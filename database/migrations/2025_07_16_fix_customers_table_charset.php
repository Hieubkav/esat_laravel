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
        // Sửa charset và collation cho bảng customers
        DB::statement('ALTER TABLE customers CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci');
        
        // Đảm bảo các cột text hỗ trợ đầy đủ UTF-8
        DB::statement('ALTER TABLE customers MODIFY COLUMN name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci');
        DB::statement('ALTER TABLE customers MODIFY COLUMN email VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci');
        DB::statement('ALTER TABLE customers MODIFY COLUMN phone VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci');
        DB::statement('ALTER TABLE customers MODIFY COLUMN address TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback vì đây là fix lỗi encoding
        // Rollback có thể gây mất dữ liệu tiếng Việt
    }
};
