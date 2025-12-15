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
        Schema::create('mshopkeeper_customers', function (Blueprint $table) {
            $table->id();

            // MShopKeeper fields
            $table->string('mshopkeeper_id')->unique()->comment('ID từ MShopKeeper API');
            $table->string('code')->nullable()->index()->comment('Mã khách hàng');
            $table->string('name')->index()->comment('Tên khách hàng');
            $table->string('tel')->nullable()->index()->comment('Số điện thoại');
            $table->string('normalized_tel')->nullable()->index()->comment('Số điện thoại chuẩn hóa');
            $table->string('standard_tel')->nullable()->index()->comment('Số điện thoại tiêu chuẩn');
            $table->text('addr')->nullable()->comment('Địa chỉ');
            $table->string('email')->nullable()->index()->comment('Email');
            $table->integer('gender')->nullable()->comment('Giới tính (0=Nam, 1=Nữ)');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->string('identify_number')->nullable()->index()->comment('Số CMND/CCCD');
            $table->string('province_addr')->nullable()->comment('Tỉnh/Thành phố');
            $table->string('district_addr')->nullable()->comment('Quận/Huyện');
            $table->string('commune_addr')->nullable()->comment('Phường/Xã');
            $table->string('membership_code')->nullable()->index()->comment('Mã thẻ thành viên');
            $table->string('member_level_id')->nullable()->index()->comment('ID hạng thẻ thành viên');
            $table->string('member_level_name')->nullable()->comment('Tên hạng thẻ thành viên');

            // Authentication fields for website login
            $table->string('password')->nullable()->comment('Mật khẩu để đăng nhập website');
            $table->string('plain_password')->nullable()->comment('Mật khẩu gốc cho admin xem');
            $table->rememberToken()->comment('Remember token cho authentication');
            $table->timestamp('email_verified_at')->nullable()->comment('Thời gian verify email');

            // Sync tracking
            $table->timestamp('last_synced_at')->nullable()->comment('Lần sync cuối từ API');
            $table->string('sync_status')->default('pending')->index()->comment('Trạng thái sync: pending, synced, error');
            $table->text('sync_error')->nullable()->comment('Lỗi sync nếu có');
            $table->json('raw_data')->nullable()->comment('Dữ liệu thô từ API');

            $table->timestamps();

            // Indexes for performance
            $table->index(['sync_status', 'last_synced_at']);
            $table->index(['gender', 'member_level_id']);
            $table->index(['province_addr', 'district_addr']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mshopkeeper_customers');
    }
};
