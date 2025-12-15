<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MShopKeeperCustomerAuthService;
use App\Http\Traits\AjaxResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ModalAuthController extends Controller
{
    use AjaxResponseTrait;

    protected $authService;

    public function __construct(MShopKeeperCustomerAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Xử lý đăng nhập từ modal
     */
    public function login(Request $request)
    {
        Log::info('Modal login attempt', ['phone' => $request->login]);

        try {
            $request->validate([
                'login' => 'required|regex:/^[0-9]{8,12}$/',
                'password' => 'required|string',
            ], [
                'login.required' => 'Vui lòng nhập số điện thoại',
                'login.regex' => 'Số điện thoại phải có 8-12 chữ số',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]);

            $phone = $request->login;
            $result = $this->authService->login($phone, $request->password);

            if ($result['success']) {
                // Regenerate session và đảm bảo nó được lưu
                $request->session()->regenerate();
                $request->session()->save();

                // Verify authentication state ngay lập tức
                if (!Auth::guard('mshopkeeper_customer')->check()) {
                    // Retry login nếu session chưa sync
                    Auth::guard('mshopkeeper_customer')->login($result['customer'], true);
                    $request->session()->save();
                }

                // Determine redirect URL - prioritize staying on current page
                $redirectUrl = $request->input('redirect_to');

                // If no redirect_to provided, stay on current page (don't redirect to home)
                if (!$redirectUrl) {
                    $redirectUrl = $request->header('referer') ?: url()->previous() ?: '/';
                }

                if ($request->expectsJson()) {
                    return $this->ajaxSuccess([
                        'user' => $result['customer'] ?? null,
                        'redirect' => $redirectUrl,
                        'stay_on_page' => true, // Flag to indicate we want to stay on current page
                        'delay_redirect' => 800, // Delay để đảm bảo session sync
                        'session_synced' => true
                    ], $result['message']);
                }

                return redirect($redirectUrl)->with('success', $result['message']);
            }

            // Xử lý các loại lỗi khác nhau
            $errorMessage = match($result['error']) {
                'CUSTOMER_NOT_FOUND' => 'Số điện thoại chưa được đăng ký. Vui lòng đăng ký tài khoản mới.',
                'NO_PASSWORD' => 'Tài khoản chưa có mật khẩu. Vui lòng tạo mật khẩu.',
                'INVALID_PASSWORD' => 'Mật khẩu không chính xác.',
                'SYSTEM_ERROR' => 'Có lỗi xảy ra. Vui lòng thử lại sau.',
                default => $result['message']
            };

            if ($request->expectsJson()) {
                return $this->ajaxValidationError([
                    'login' => $errorMessage
                ]);
            }

            // Redirect back với lỗi và session key riêng cho modal
            return redirect()->back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => $errorMessage])
                ->with('modal_login_error', true);

        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return $this->ajaxValidationError($e->errors());
            }

            return redirect()->back()
                ->withInput($request->only('login'))
                ->withErrors($e->errors())
                ->with('modal_login_error', true);
        } catch (\Exception $e) {
            Log::error('Modal login error', ['error' => $e->getMessage()]);

            $errorMessage = 'Có lỗi xảy ra. Vui lòng thử lại sau.';

            if ($request->expectsJson()) {
                return $this->ajaxServerError($errorMessage);
            }

            return redirect()->back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => $errorMessage])
                ->with('modal_login_error', true);
        }
    }

    /**
     * Xử lý đăng ký từ modal
     */
    public function register(Request $request)
    {
        Log::info('Modal registration started', ['request_data' => $request->except('password', 'password_confirmation')]);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'required|regex:/^[0-9]{8,12}$/',
                'password' => 'required|string|min:6|confirmed',
                'gender' => 'nullable|in:0,1',
                'address' => 'nullable|string|max:500',
                'identify_number' => 'nullable|string|max:20',
            ], [
                'name.required' => 'Vui lòng nhập họ và tên',
                'name.string' => 'Họ và tên phải là chuỗi ký tự',
                'name.max' => 'Họ và tên không được vượt quá 255 ký tự',
                'email.email' => 'Email không đúng định dạng',
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'phone.regex' => 'Số điện thoại phải có 8-12 chữ số',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp',
                'gender.in' => 'Giới tính không hợp lệ',
                'address.max' => 'Địa chỉ không được vượt quá 500 ký tự',
                'identify_number.max' => 'Số CMND/CCCD không được vượt quá 20 ký tự',
            ]);

            Log::info('Modal validation passed, calling authService->register');

            $result = $this->authService->register([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'gender' => $request->gender ?? 0,
                'address' => $request->address,
                'identify_number' => $request->identify_number,
            ]);

            Log::info('Modal AuthService register result', ['success' => $result['success'], 'error' => $result['error'] ?? null]);

            if ($result['success']) {
                // Regenerate session và đảm bảo nó được lưu
                $request->session()->regenerate();
                $request->session()->save();

                // Verify authentication state ngay lập tức
                if (!Auth::guard('mshopkeeper_customer')->check()) {
                    // Retry login nếu session chưa sync
                    Auth::guard('mshopkeeper_customer')->login($result['customer'], true);
                    $request->session()->save();
                }

                // Determine redirect URL - prioritize staying on current page
                $redirectUrl = $request->input('redirect_to');

                // If no redirect_to provided, stay on current page (don't redirect to home)
                if (!$redirectUrl) {
                    $redirectUrl = $request->header('referer') ?: url()->previous() ?: '/';
                }

                if ($request->expectsJson()) {
                    return $this->ajaxSuccess([
                        'user' => $result['customer'] ?? null,
                        'redirect' => $redirectUrl,
                        'stay_on_page' => true, // Flag to indicate we want to stay on current page
                        'delay_redirect' => 800, // Delay để đảm bảo session sync
                        'session_synced' => true
                    ], $result['message']);
                }

                return redirect($redirectUrl)->with('success', $result['message']);
            }

            // Xử lý các loại lỗi
            if ($result['error'] === 'CUSTOMER_EXISTS_WITH_PASSWORD') {
                if ($request->expectsJson()) {
                    return $this->ajaxValidationError([
                        'phone' => $result['message']
                    ]);
                }

                return redirect()->back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['phone' => $result['message']])
                    ->with('modal_register_error', true);
            }

            if ($result['error'] === 'CUSTOMER_EXISTS_NO_PASSWORD') {
                // Lưu thông tin customer vào session để tạo mật khẩu
                $customerData = $result['existing_customer'] ?? null;

                if (!$customerData) {
                    Log::error('Missing customer data in CUSTOMER_EXISTS_NO_PASSWORD response', [
                        'result' => $result
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
                    ], 500);
                }

                session(['mshopkeeper_customer' => $customerData]);

                Log::info('Customer data saved to session for password creation', [
                    'customer_id' => $customerData['CustomerID'] ?? $customerData['Id'] ?? 'unknown',
                    'customer_name' => $customerData['CustomerName'] ?? $customerData['Name'] ?? 'unknown'
                ]);

                if ($request->expectsJson()) {
                    return $this->ajaxSuccess([
                        'show_password_form' => true,
                        'customer_data' => $customerData,
                        'customer_name' => $customerData['CustomerName'] ?? $customerData['Name'] ?? 'Khách hàng'
                    ], $result['message']);
                }

                // Fallback cho non-AJAX request (không nên xảy ra với modal)
                return back()->with('error', 'Vui lòng sử dụng modal để đăng ký.');
            }

            // Lỗi khác
            $errorMessage = $result['message'] ?? 'Có lỗi xảy ra. Vui lòng thử lại sau.';

            if ($request->expectsJson()) {
                return $this->ajaxValidationError([
                    'phone' => $errorMessage
                ]);
            }

            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['phone' => $errorMessage])
                ->with('modal_register_error', true);

        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return $this->ajaxValidationError($e->errors());
            }

            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors($e->errors())
                ->with('modal_register_error', true);

        } catch (\Exception $e) {
            Log::error('Modal registration error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            $errorMessage = 'Có lỗi xảy ra. Vui lòng thử lại sau.';

            if ($request->expectsJson()) {
                return $this->ajaxServerError($errorMessage);
            }

            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['phone' => $errorMessage])
                ->with('modal_register_error', true);
        }
    }

    /**
     * Tạo mật khẩu cho khách hàng đã tồn tại
     */
    public function createPassword(Request $request)
    {
        Log::info('Create password method called', [
            'session_has_customer' => session()->has('mshopkeeper_customer')
        ]);
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if (!session()->has('mshopkeeper_customer')) {
            Log::error('Session mshopkeeper_customer not found', [
                'all_session_keys' => array_keys(session()->all())
            ]);

            if ($request->expectsJson()) {
                return $this->ajaxError(['general' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại.']);
            }
            return back()->with('error', 'Phiên làm việc đã hết hạn.');
        }

        $mshopkeeperCustomer = session('mshopkeeper_customer');
        $password = $request->input('password');

        try {
            $authService = new \App\Services\MShopKeeperCustomerAuthService(
                new \App\Services\MShopKeeperService()
            );
            $result = $authService->createPasswordForExisting($mshopkeeperCustomer, $password);

            if ($result['success']) {
                // Xóa session data
                session()->forget('mshopkeeper_customer');

                if ($request->expectsJson()) {
                    return $this->ajaxSuccess([
                        'user' => $result['customer'],
                        'password_created' => true,
                        'trigger_auth_refresh' => true
                    ], $result['message']);
                }

                return back()->with('success', $result['message']);
            }

            if ($request->expectsJson()) {
                return $this->ajaxError(['general' => $result['message']]);
            }

            return back()->with('error', $result['message']);

        } catch (\Exception $e) {
            Log::error('Create password error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->ajaxError(['general' => 'Có lỗi xảy ra. Vui lòng thử lại sau.']);
            }

            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
        }
    }
}
