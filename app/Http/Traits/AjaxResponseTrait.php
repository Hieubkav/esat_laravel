<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait for standardizing AJAX JSON responses
 */
trait AjaxResponseTrait
{
    /**
     * Return standardized JSON success response
     *
     * @param array $data Response data
     * @param string|null $message Success message
     * @param int $status HTTP status code
     * @return JsonResponse
     */
    protected function ajaxSuccess(array $data = [], ?string $message = null, int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message ?? 'Thành công',
            'user' => $data['user'] ?? null,
            'redirect' => $data['redirect'] ?? null,
            'timestamp' => now()->toISOString(),
        ];

        // Merge data directly into response (không wrap trong 'data' key)
        $response = array_merge($response, $data);

        return response()->json($response, $status);
    }

    /**
     * Return standardized JSON error response
     *
     * @param array $errors Validation or other errors
     * @param string|null $message Error message
     * @param string|null $errorType Error type identifier
     * @param int $status HTTP status code
     * @return JsonResponse
     */
    protected function ajaxError(
        array $errors = [], 
        ?string $message = null, 
        ?string $errorType = null, 
        int $status = 422
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message ?? 'Có lỗi xảy ra',
            'errors' => $errors,
            'error_type' => $errorType,
            'timestamp' => now()->toISOString(),
        ], $status);
    }

    /**
     * Return JSON response for authentication success
     *
     * @param mixed $user User object
     * @param string $message Success message
     * @param string|null $redirectUrl Redirect URL
     * @return JsonResponse
     */
    protected function ajaxAuthSuccess($user, string $message, ?string $redirectUrl = null): JsonResponse
    {
        return $this->ajaxSuccess([
            'user' => $user,
            'redirect' => $redirectUrl ?? '/',
            'auth_status' => 'authenticated'
        ], $message);
    }

    /**
     * Return JSON response for authentication failure
     *
     * @param array $errors Validation errors
     * @param string $message Error message
     * @return JsonResponse
     */
    protected function ajaxAuthError(array $errors, string $message): JsonResponse
    {
        return $this->ajaxError($errors, $message, 'AUTH_FAILED', 401);
    }

    /**
     * Return JSON response for validation errors
     *
     * @param array $errors Validation errors
     * @param string|null $message Error message
     * @return JsonResponse
     */
    protected function ajaxValidationError(array $errors, ?string $message = null): JsonResponse
    {
        return $this->ajaxError(
            $errors, 
            $message ?? 'Dữ liệu không hợp lệ', 
            'VALIDATION_FAILED', 
            422
        );
    }

    /**
     * Return JSON response for server errors
     *
     * @param string|null $message Error message
     * @param \Exception|null $exception Exception object for logging
     * @return JsonResponse
     */
    protected function ajaxServerError(?string $message = null, ?\Exception $exception = null): JsonResponse
    {
        if ($exception) {
            \Log::error('Server error in AJAX request', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        }

        return $this->ajaxError(
            [], 
            $message ?? 'Có lỗi xảy ra trên máy chủ. Vui lòng thử lại sau.', 
            'SERVER_ERROR', 
            500
        );
    }

    /**
     * Return JSON response for unauthorized access
     *
     * @param string|null $message Error message
     * @return JsonResponse
     */
    protected function ajaxUnauthorized(?string $message = null): JsonResponse
    {
        return $this->ajaxError(
            [], 
            $message ?? 'Bạn không có quyền truy cập.', 
            'UNAUTHORIZED', 
            403
        );
    }

    /**
     * Return JSON response for not found resources
     *
     * @param string|null $message Error message
     * @return JsonResponse
     */
    protected function ajaxNotFound(?string $message = null): JsonResponse
    {
        return $this->ajaxError(
            [], 
            $message ?? 'Không tìm thấy tài nguyên yêu cầu.', 
            'NOT_FOUND', 
            404
        );
    }

    /**
     * Check if request expects JSON response
     *
     * @param \Illuminate\Http\Request|null $request
     * @return bool
     */
    protected function expectsJson($request = null): bool
    {
        $request = $request ?? request();
        
        return $request->expectsJson() || 
               $request->ajax() || 
               $request->wantsJson() ||
               $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Return appropriate response based on request type
     *
     * @param mixed $ajaxResponse Response for AJAX requests
     * @param mixed $webResponse Response for web requests
     * @param \Illuminate\Http\Request|null $request
     * @return mixed
     */
    protected function respondBasedOnRequest($ajaxResponse, $webResponse, $request = null)
    {
        return $this->expectsJson($request) ? $ajaxResponse : $webResponse;
    }

    /**
     * Handle exception and return appropriate response
     *
     * @param \Exception $exception
     * @param string|null $defaultMessage
     * @param \Illuminate\Http\Request|null $request
     * @return mixed
     */
    protected function handleException(\Exception $exception, ?string $defaultMessage = null, $request = null)
    {
        $message = $defaultMessage ?? 'Có lỗi xảy ra. Vui lòng thử lại sau.';
        
        if ($this->expectsJson($request)) {
            return $this->ajaxServerError($message, $exception);
        }
        
        // For web requests, you might want to redirect or show error page
        throw $exception;
    }
}
