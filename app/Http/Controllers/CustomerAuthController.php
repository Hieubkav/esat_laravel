<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MShopKeeperCustomerAuthService;
use App\Http\Traits\AjaxResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\MShopKeeperCustomer;

class CustomerAuthController extends Controller
{
    use AjaxResponseTrait;

    protected $authService;

    public function __construct(MShopKeeperCustomerAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        return view('customer.login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string',
                'password' => 'required|string',
            ], [
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]);

            // Support both 'login' and 'phone' field names for backward compatibility
            $phone = $request->input('phone') ?: $request->input('login');

            // Chỉ hỗ trợ đăng nhập bằng số điện thoại
            if (!preg_match('/^[0-9]{8,12}$/', $phone)) {
                if ($request->expectsJson()) {
                    return $this->ajaxValidationError([
                        'phone' => 'Vui lòng nhập số điện thoại hợp lệ (8-12 chữ số).'
                    ]);
                }

                throw ValidationException::withMessages([
                    'login' => 'Vui lòng nhập số điện thoại hợp lệ (8-12 chữ số).',
                ]);
            }

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

                // Determine redirect URL with priority: redirect_to > intended > home
                $redirectUrl = $request->input('redirect_to') ?:
                              (session()->has('url.intended') ? session('url.intended') : '/');

                if ($request->expectsJson()) {
                    return $this->ajaxAuthSuccess(
                        $result['customer'],
                        $result['message'],
                        $redirectUrl
                    );
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
                return $this->ajaxAuthError([
                    'phone' => $errorMessage
                ], $errorMessage);
            }

            throw ValidationException::withMessages([
                'login' => $errorMessage,
            ]);

        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return $this->jsonErrorResponse($e->errors());
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->jsonErrorResponse([
                    'phone' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
                ]);
            }
            throw $e;
        }
    }

    public function logout(Request $request)
    {
        try {
            // Chỉ logout guard mshopkeeper_customer, không invalidate toàn bộ session
            $this->authService->logout();

            // Kiểm tra xem có admin đang đăng nhập không
            $hasAdminSession = Auth::guard('web')->check();

            if (!$hasAdminSession) {
                // Chỉ regenerate CSRF token nếu không có admin session
                $request->session()->regenerateToken();
            } else {
                // Nếu có admin session, chỉ regenerate token mà không làm gì khác
                $request->session()->regenerateToken();
                Log::info('Customer logout: Admin session detected, preserving admin session');
            }

            // Determine redirect URL - stay on current page unless specified
            $redirectUrl = $request->input('redirect_to');
            if (!$redirectUrl) {
                $redirectUrl = $request->header('referer') ?: url()->previous() ?: '/';
            }

            if ($request->expectsJson()) {
                return $this->jsonSuccessResponse([
                    'message' => 'Đăng xuất thành công!',
                    'redirect' => $redirectUrl,
                    'logout' => true, // Flag to indicate this is a logout response
                    'stay_on_page' => true // Flag to indicate we want to stay on current page
                ]);
            }

            // Redirect to current page instead of home
            return redirect($redirectUrl)->with('success', 'Đăng xuất thành công!');
        } catch (\Exception $e) {
            Log::error('Logout error', ['error' => $e->getMessage()]);

            // Fallback redirect URL
            $redirectUrl = $request->input('redirect_to') ?: '/';

            if ($request->expectsJson()) {
                return $this->jsonSuccessResponse([
                    'message' => 'Đăng xuất thành công!',
                    'redirect' => $redirectUrl,
                    'logout' => true,
                    'stay_on_page' => true
                ]);
            }

            return redirect($redirectUrl)->with('success', 'Đăng xuất thành công!');
        }
    }

    public function showRegisterForm()
    {
        return view('customer.register');
    }

    public function register(Request $request)
    {
        Log::info('Customer registration started', ['request_data' => $request->except('password', 'password_confirmation')]);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'required|regex:/^[0-9]{10,11}$/',
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
                'phone.regex' => 'Số điện thoại phải có 10-11 chữ số',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp',
                'gender.in' => 'Giới tính không hợp lệ',
                'address.max' => 'Địa chỉ không được vượt quá 500 ký tự',
                'identify_number.max' => 'Số CMND/CCCD không được vượt quá 20 ký tự',
            ]);

            Log::info('Validation passed, calling authService->register');

            $result = $this->authService->register([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'gender' => $request->gender ?? 0,
                'address' => $request->address,
                'identify_number' => $request->identify_number,
            ]);

            Log::info('AuthService register result', ['success' => $result['success'], 'error' => $result['error'] ?? null]);

            if (!($result['success'] ?? false)) {
                return response()->json(['message' => $result['message'] ?? 'Đăng ký thất bại'], 422);
            }

            return response()->json([
                'message'  => $result['message'] ?? 'Đăng ký thành công',
                'customer' => $result['customer'],
            ]);

        } catch (ValidationException $e) {
            Log::warning('Validation failed', ['errors' => $e->errors()]);

            if ($request->expectsJson()) {
                return $this->jsonErrorResponse($e->errors());
            }

            throw $e;
        } catch (\Exception $e) {
            Log::error('Registration failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return $this->jsonErrorResponse([
                    'phone' => 'Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại sau.'
                ]);
            }

            throw ValidationException::withMessages([
                'phone' => 'Có lỗi xảy ra trong quá trình đăng ký. Chi tiết: ' . $e->getMessage(),
            ]);
        }
    }

    public function showProfile()
    {
        if (!$this->authService->check()) {
            return redirect()->route('customer.login')->with('error', 'Vui lòng đăng nhập để xem thông tin cá nhân.');
        }

        $customer = $this->authService->user();
        return view('customer.profile', compact('customer'));
    }

    /**
     * Hiển thị form tạo mật khẩu cho khách hàng đã tồn tại
     */
    public function showCreatePasswordForm()
    {
        if (!session()->has('mshopkeeper_customer')) {
            return redirect()->route('customer.register')->with('error', 'Phiên làm việc đã hết hạn.');
        }

        $mshopkeeperCustomer = session('mshopkeeper_customer');
        return view('customer.create-password', compact('mshopkeeperCustomer'));
    }

    /**
     * Check phone number realtime để auto-fill form
     */
    public function checkPhone(string $phone)
    {
        try {
            // Validate phone format
            if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
                return $this->ajaxValidationError([
                    'phone' => 'Số điện thoại không đúng định dạng'
                ]);
            }

            $status = $this->authService->checkCustomerStatus($phone);

            switch ($status['status']) {
                case 'HAS_PASSWORD':
                    return response()->json([
                        'success' => true,
                        'status' => 'HAS_PASSWORD',
                        'message' => 'Số điện thoại đã có tài khoản. Vui lòng đăng nhập.'
                    ]);

                case 'NEEDS_PASSWORD':
                    // Trả kèm data để FE autofill & disable field
                    return response()->json([
                        'success' => true,
                        'status' => 'NEEDS_PASSWORD',
                        'message' => 'Tài khoản đã tồn tại. Vui lòng tạo mật khẩu.',
                        'customer' => $status['customer'] ?? null
                    ]);

                case 'NOT_FOUND':
                    return response()->json([
                        'success' => true,
                        'status' => 'NOT_FOUND',
                        'message' => 'Số điện thoại chưa được đăng ký.'
                    ]);

                default:
                    return response()->json([
                        'success' => false,
                        'status' => 'ERROR',
                        'message' => 'Có lỗi xảy ra khi kiểm tra số điện thoại.'
                    ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Check phone error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'status' => 'ERROR',
                'message' => 'Có lỗi xảy ra khi kiểm tra số điện thoại.'
            ], 500);
        }
    }

    /**
     * Xử lý tạo mật khẩu cho khách hàng đã tồn tại
     */
    public function createPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        if (!session()->has('mshopkeeper_customer')) {
            return redirect()->route('storeFront')->with('error', 'Phiên làm việc đã hết hạn.');
        }

        $mshopkeeperCustomer = session('mshopkeeper_customer');

        $result = $this->authService->createPasswordForExisting($mshopkeeperCustomer, $request->password);

        if ($result['success']) {
            session()->forget('mshopkeeper_customer');

            // Regenerate session và đảm bảo nó được lưu
            $request->session()->regenerate();
            $request->session()->save();

            // Verify authentication state ngay lập tức
            if (!Auth::guard('mshopkeeper_customer')->check()) {
                // Retry login nếu session chưa sync
                Auth::guard('mshopkeeper_customer')->login($result['customer'], true);
                $request->session()->save();
            }

            // Determine redirect URL with priority: redirect_to > intended > home
            $redirectUrl = $request->input('redirect_to') ?:
                          (session()->has('url.intended') ? session('url.intended') : '/');

            return redirect($redirectUrl)->with('success', $result['message']);
        }

        throw ValidationException::withMessages([
            'password' => $result['message'],
        ]);
    }

    /**
     * Return JSON success response for AJAX requests
     */
    protected function jsonSuccessResponse(array $data = [], int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $data['message'] ?? 'Thành công',
            'data' => $data,
            'user' => $data['user'] ?? null,
            'redirect' => $data['redirect'] ?? null,
        ], $status);
    }

    /**
     * Return JSON error response for AJAX requests
     */
    protected function jsonErrorResponse(array $errors = [], ?string $errorType = null, int $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra',
            'errors' => $errors,
            'error_type' => $errorType,
        ], $status);
    }

    /**
     * API: Check phone number status for realtime detection
     */
    public function checkPhoneStatus($phone)
    {
        try {
            $result = $this->authService->checkCustomerStatus($phone);

            return response()->json([
                'success' => $result['success'],
                'status' => $result['status'] ?? null,
                'message' => $result['message'] ?? null,
                'customer_data' => $result['customer_data'] ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra khi kiểm tra số điện thoại.'
            ], 500);
        }
    }

    /**
     * API: Verify customer identity
     */
    public function verifyIdentity(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name' => 'required|string|min:2',
            'email' => 'nullable|email',
        ]);

        try {
            $result = $this->authService->verifyCustomerIdentity(
                $request->phone,
                $request->only(['name', 'email'])
            );

            if ($result['success'] && $result['verified']) {
                // Lưu verification token vào session
                session([
                    'customer_verification' => [
                        'phone' => $request->phone,
                        'verified_at' => now(),
                        'customer_data' => $result['customer_data']
                    ]
                ]);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra khi xác thực thông tin.'
            ], 500);
        }
    }

    /**
     * API: Create password after verification
     */
    public function createPasswordVerified(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Check verification session
        if (!session()->has('customer_verification')) {
            return response()->json([
                'success' => false,
                'error' => 'VERIFICATION_REQUIRED',
                'message' => 'Vui lòng xác thực thông tin trước khi tạo mật khẩu.'
            ], 400);
        }

        $verification = session('customer_verification');

        // Check verification timeout (15 minutes)
        if (now()->diffInMinutes($verification['verified_at']) > 15) {
            session()->forget('customer_verification');
            return response()->json([
                'success' => false,
                'error' => 'VERIFICATION_EXPIRED',
                'message' => 'Phiên xác thực đã hết hạn. Vui lòng xác thực lại.'
            ], 400);
        }

        try {
            $result = $this->authService->createPasswordForExisting(
                $verification['customer_data'],
                $request->password
            );

            // Clear verification session
            session()->forget('customer_verification');

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'customer' => $result['customer'],
                    'redirect' => url()->previous() ?: '/'
                ]);
            }

            return response()->json($result, 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra khi tạo mật khẩu.'
            ], 500);
        }
    }
}
