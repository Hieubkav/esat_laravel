<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop employee_images trước vì có foreign key
        Schema::dropIfExists('employee_images');
        Schema::dropIfExists('employees');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không rollback - dữ liệu đã mất
    }
};
