<?php

namespace App\Services;

use App\Models\MShopKeeperCustomer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PasswordResetService
{
    /**
     * Gửi link reset password qua email
     */
    public function sendResetLink($phone)
    {
        try {
            // Tìm khách hàng theo số điện thoại
            $customer = MShopKeeperCustomer::where('tel', $phone)->first();
            
            if (!$customer) {
                return [
                    'success' => false,
                    'error' => 'CUSTOMER_NOT_FOUND',
                    'message' => 'Số điện thoại chưa được đăng ký.'
                ];
            }
            
            if (!$customer->canResetPasswordViaEmail()) {
                return [
                    'success' => false,
                    'error' => 'NO_EMAIL',
                    'message' => 'Tài khoản chưa có email. Vui lòng liên hệ admin để được hỗ trợ.'
                ];
            }
            
            // Tạo token reset
            $token = Str::random(64);
            $cacheKey = "password_reset_{$token}";
            
            // Lưu token vào cache (15 phút)
            Cache::put($cacheKey, [
                'customer_id' => $customer->id,
                'phone' => $phone,
                'created_at' => now()
            ], 900); // 15 minutes
            
            // Gửi email
            $resetUrl = route('customer.password.reset', ['token' => $token]);
            
            Mail::send('emails.password-reset', [
                'customer' => $customer,
                'resetUrl' => $resetUrl,
                'token' => $token
            ], function ($message) use ($customer) {
                $message->to($customer->email, $customer->name)
                        ->subject('Đặt lại mật khẩu - ' . config('app.name'));
            });
            
            Log::info('Password reset email sent', [
                'customer_id' => $customer->id,
                'email' => $customer->email
            ]);
            
            return [
                'success' => true,
                'message' => 'Link đặt lại mật khẩu đã được gửi đến email của bạn.'
            ];
            
        } catch (\Exception $e) {
            Log::error('Send reset link failed', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            
            return [
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ];
        }
    }
    
    /**
     * Verify token reset password
     */
    public function verifyResetToken($token)
    {
        $cacheKey = "password_reset_{$token}";
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            return [
                'success' => false,
                'error' => 'INVALID_TOKEN',
                'message' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.'
            ];
        }
        
        $customer = MShopKeeperCustomer::find($data['customer_id']);
        
        if (!$customer) {
            return [
                'success' => false,
                'error' => 'CUSTOMER_NOT_FOUND',
                'message' => 'Khách hàng không tồn tại.'
            ];
        }
        
        return [
            'success' => true,
            'customer' => $customer,
            'data' => $data
        ];
    }
    
    /**
     * Reset password với token
     */
    public function resetPassword($token, $newPassword)
    {
        try {
            $verifyResult = $this->verifyResetToken($token);
            
            if (!$verifyResult['success']) {
                return $verifyResult;
            }
            
            $customer = $verifyResult['customer'];
            
            // Cập nhật password
            $customer->update([
                'password' => Hash::make($newPassword),
                'plain_password' => $newPassword
            ]);
            
            // Xóa token khỏi cache
            $cacheKey = "password_reset_{$token}";
            Cache::forget($cacheKey);
            
            Log::info('Password reset successfully', [
                'customer_id' => $customer->id
            ]);
            
            return [
                'success' => true,
                'customer' => $customer,
                'message' => 'Đặt lại mật khẩu thành công!'
            ];
            
        } catch (\Exception $e) {
            Log::error('Reset password failed', [
                'error' => $e->getMessage(),
                'token' => $token
            ]);
            
            return [
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ];
        }
    }
    
    /**
     * Đổi mật khẩu (cần mật khẩu cũ)
     */
    public function changePassword($customer, $oldPassword, $newPassword)
    {
        try {
            // Kiểm tra mật khẩu cũ
            if (!Hash::check($oldPassword, $customer->password)) {
                return [
                    'success' => false,
                    'error' => 'INVALID_OLD_PASSWORD',
                    'message' => 'Mật khẩu cũ không chính xác.'
                ];
            }
            
            // Cập nhật mật khẩu mới
            $customer->update([
                'password' => Hash::make($newPassword),
                'plain_password' => $newPassword
            ]);
            
            Log::info('Password changed successfully', [
                'customer_id' => $customer->id
            ]);
            
            return [
                'success' => true,
                'message' => 'Đổi mật khẩu thành công!'
            ];
            
        } catch (\Exception $e) {
            Log::error('Change password failed', [
                'error' => $e->getMessage(),
                'customer_id' => $customer->id
            ]);
            
            return [
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ];
        }
    }
}
