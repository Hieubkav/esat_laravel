<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('post_views');
        Schema::dropIfExists('product_views');
    }

    public function down(): void
    {
        // Tables will not be recreated
    }
};
