<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\QrCodeService;
use Illuminate\Console\Command;

class GenerateEmployeeQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:generate-qr-codes {--force : Force regenerate all QR codes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for all employees';

    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        parent::__construct();
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        $query = Employee::where('status', 'active')->whereNotNull('slug');

        if (!$force) {
            $query->whereNull('qr_code');
        }

        $employees = $query->get();

        if ($employees->isEmpty()) {
            $this->info('Không có nhân viên nào cần tạo QR code.');
            return;
        }

        $this->info("Đang tạo QR code cho {$employees->count()} nhân viên...");

        $progressBar = $this->output->createProgressBar($employees->count());
        $progressBar->start();

        $success = 0;
        $failed = 0;

        foreach ($employees as $employee) {
            try {
                $profileUrl = route('employee.profile', $employee->slug);
                $qrCodePath = $this->qrCodeService->generateEmployeeQrCode($profileUrl, $employee->name);

                if ($qrCodePath) {
                    // Xóa QR code cũ nếu có và force = true
                    if ($force && $employee->qr_code) {
                        $this->qrCodeService->deleteQrCode($employee->qr_code);
                    }

                    $employee->updateQuietly(['qr_code' => $qrCodePath]);
                    $success++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("Lỗi tạo QR code cho {$employee->name}: " . $e->getMessage());
                $failed++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Hoàn thành!");
        $this->info("Thành công: {$success}");
        if ($failed > 0) {
            $this->error("Thất bại: {$failed}");
        }
    }
}
