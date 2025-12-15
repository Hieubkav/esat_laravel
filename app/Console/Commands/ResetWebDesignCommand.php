<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WebDesignService;

class ResetWebDesignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdesign:reset {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset WebDesign configuration to default values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Bạn có chắc chắn muốn reset tất cả cấu hình WebDesign về mặc định?')) {
                $this->info('Hủy bỏ reset.');
                return 0;
            }
        }

        $this->info('Đang reset cấu hình WebDesign...');

        $webDesignService = app(WebDesignService::class);
        $webDesignService->resetToDefault();

        $this->info('✅ Đã reset cấu hình WebDesign về mặc định thành công!');
        $this->line('');
        $this->line('Các section đã được khôi phục:');

        $sections = \App\Models\WebDesign::getDefaultSections();
        foreach ($sections as $key => $config) {
            $this->line("  • {$config['name']} ({$key})");
        }

        return 0;
    }
}
