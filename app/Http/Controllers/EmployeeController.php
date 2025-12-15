<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Hiển thị danh sách nhân viên (chỉ dành cho user đã đăng nhập)
     */
    public function index()
    {
        $employees = Employee::where('status', 'active')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('employee.index', compact('employees'));
    }

    /**
     * Hiển thị trang thông tin nhân viên
     */
    public function profile(string $slug)
    {
        $employee = Employee::where('slug', $slug)
            ->where('status', 'active')
            ->with(['images' => function ($query) {
                $query->where('status', 'active')->orderBy('order');
            }])
            ->firstOrFail();

        return view('employee.profile', compact('employee'));
    }

    /**
     * Tạo và tải về QR code
     */
    public function downloadQrCode(string $slug)
    {
        $employee = Employee::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $profileUrl = route('employee.profile', $employee->slug);
        $qrCodeData = $this->qrCodeService->generateQrCodeForDownload($profileUrl);

        $fileName = 'qr-code-' . $employee->slug . '.svg';

        return response($qrCodeData)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Hiển thị QR code trực tiếp
     */
    public function showQrCode(string $slug)
    {
        $employee = Employee::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $profileUrl = route('employee.profile', $employee->slug);
        $qrCodeData = $this->qrCodeService->generateQrCodeForDownload($profileUrl);

        return response($qrCodeData)
            ->header('Content-Type', 'image/svg+xml');
    }
}
