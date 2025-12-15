<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PasswordResetService;
use App\Services\MShopKeeperCustomerAuthService;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    protected $passwordResetService;
    protected $authService;
    
    public function __construct(
        PasswordResetService $passwordResetService,
        MShopKeeperCustomerAuthService $authService
    ) {
        $this->passwordResetService = $passwordResetService;
        $this->authService = $authService;
    }
    
    /**
     * Hiển thị form nhập số điện thoại để reset password
     */
    public function showResetRequestForm()
    {
        return view('customer.password.reset-request');
    }
    
    /**
     * Gửi link reset password qua email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10,11}$/',
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.regex' => 'Số điện thoại phải có 10-11 chữ số',
        ]);
        
        $result = $this->passwordResetService->sendResetLink($request->phone);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        
        $errorMessage = match($result['error']) {
            'CUSTOMER_NOT_FOUND' => 'Số điện thoại chưa được đăng ký.',
            'NO_EMAIL' => 'Tài khoản chưa có email. Vui lòng liên hệ admin để được hỗ trợ.',
            'SYSTEM_ERROR' => 'Có lỗi xảy ra. Vui lòng thử lại sau.',
            default => $result['message']
        };
        
        throw ValidationException::withMessages([
            'phone' => $errorMessage,
        ]);
    }
    
    /**
     * Hiển thị form nhập mật khẩu mới
     */
    public function showResetForm($token)
    {
        $verifyResult = $this->passwordResetService->verifyResetToken($token);
        
        if (!$verifyResult['success']) {
            return redirect()->route('customer.password.request')
                ->with('error', $verifyResult['message']);
        }
        
        return view('customer.password.reset-form', [
            'token' => $token,
            'customer' => $verifyResult['customer']
        ]);
    }
    
    /**
     * Xử lý reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'token.required' => 'Token không hợp lệ',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);
        
        $result = $this->passwordResetService->resetPassword($request->token, $request->password);
        
        if ($result['success']) {
            return redirect()->route('customer.login')
                ->with('success', $result['message']);
        }
        
        $errorMessage = match($result['error']) {
            'INVALID_TOKEN' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
            'CUSTOMER_NOT_FOUND' => 'Khách hàng không tồn tại.',
            'SYSTEM_ERROR' => 'Có lỗi xảy ra. Vui lòng thử lại sau.',
            default => $result['message']
        };
        
        throw ValidationException::withMessages([
            'password' => $errorMessage,
        ]);
    }
    
    /**
     * Hiển thị form đổi mật khẩu (cần đăng nhập)
     */
    public function showChangePasswordForm()
    {
        if (!$this->authService->check()) {
            return redirect()->route('customer.login')->with('error', 'Vui lòng đăng nhập để đổi mật khẩu.');
        }
        
        $customer = $this->authService->user();
        return view('customer.password.change', compact('customer'));
    }
    
    /**
     * Xử lý đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        if (!$this->authService->check()) {
            return redirect()->route('customer.login')->with('error', 'Vui lòng đăng nhập để đổi mật khẩu.');
        }
        
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);
        
        $customer = $this->authService->user();
        
        $result = $this->passwordResetService->changePassword(
            $customer,
            $request->current_password,
            $request->password
        );
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        
        $errorMessage = match($result['error']) {
            'INVALID_OLD_PASSWORD' => 'Mật khẩu hiện tại không chính xác.',
            'SYSTEM_ERROR' => 'Có lỗi xảy ra. Vui lòng thử lại sau.',
            default => $result['message']
        };
        
        throw ValidationException::withMessages([
            'current_password' => $errorMessage,
        ]);
    }
}
