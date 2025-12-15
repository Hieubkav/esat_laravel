<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncMShopKeeperInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout
    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry after 1, 2, 5 minutes

    protected string $fromDate;
    protected string $toDate;
    protected array $options;

    /**
     * Create a new job instance.
     */
    public function __construct(string $fromDate, string $toDate, array $options = [])
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting MShopKeeper invoices sync job', [
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate,
                'options' => $this->options
            ]);

            // Build command arguments
            $arguments = [
                '--from-date' => $this->fromDate,
                '--to-date' => $this->toDate,
            ];

            // Add optional arguments
            if (isset($this->options['customer_id'])) {
                $arguments['--customer-id'] = $this->options['customer_id'];
            }

            if (isset($this->options['branch_id'])) {
                $arguments['--branch-id'] = $this->options['branch_id'];
            }

            if (isset($this->options['force']) && $this->options['force']) {
                $arguments['--force'] = true;
            }

            // Execute sync command
            $exitCode = Artisan::call('mshopkeeper:sync-invoices', $arguments);

            if ($exitCode === 0) {
                Log::info('MShopKeeper invoices sync job completed successfully', [
                    'from_date' => $this->fromDate,
                    'to_date' => $this->toDate,
                    'output' => Artisan::output()
                ]);
            } else {
                throw new \Exception('Sync command failed with exit code: ' . $exitCode);
            }

        } catch (\Exception $e) {
            Log::error('MShopKeeper invoices sync job failed', [
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('MShopKeeper invoices sync job failed permanently', [
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'mshopkeeper',
            'invoices',
            'sync',
            'from:' . $this->fromDate,
            'to:' . $this->toDate
        ];
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return $this->backoff;
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): Carbon
    {
        return now()->addHours(2);
    }

    /**
     * Static method to dispatch sync job easily
     */
    public static function dispatchSync(string $fromDate, string $toDate, array $options = []): void
    {
        static::dispatch($fromDate, $toDate, $options)
            ->onQueue('mshopkeeper')
            ->delay(now()->addSeconds(5)); // Small delay to avoid conflicts
    }

    /**
     * Static method to dispatch daily sync
     */
    public static function dispatchDailySync(int $days = 1): void
    {
        $toDate = now()->format('Y-m-d');
        $fromDate = now()->subDays($days)->format('Y-m-d');
        
        static::dispatchSync($fromDate, $toDate, ['force' => false]);
    }

    /**
     * Static method to dispatch weekly sync
     */
    public static function dispatchWeeklySync(): void
    {
        $toDate = now()->format('Y-m-d');
        $fromDate = now()->subDays(7)->format('Y-m-d');
        
        static::dispatchSync($fromDate, $toDate, ['force' => true]);
    }
}
