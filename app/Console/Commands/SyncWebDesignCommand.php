<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebDesign;
use App\Services\WebDesignService;
use Illuminate\Support\Facades\File;

class SyncWebDesignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdesign:sync {--check : Only check for missing sections}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync WebDesign sections with actual components in views';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Đang đồng bộ WebDesign sections...');

        // Lấy danh sách sections từ default config
        $defaultSections = WebDesign::getDefaultSections();

        // Lấy danh sách sections hiện có trong database
        $existingSections = WebDesign::all()->pluck('section_key')->toArray();

        // Tìm sections thiếu
        $missingSections = array_diff(array_keys($defaultSections), $existingSections);

        // Tìm sections thừa (không có trong default)
        $extraSections = array_diff($existingSections, array_keys($defaultSections));

        if ($this->option('check')) {
            $this->displayCheckResults($missingSections, $extraSections, $defaultSections);
            return 0;
        }

        // Thêm sections thiếu
        if (!empty($missingSections)) {
            $this->info("📝 Thêm {count($missingSections)} sections thiếu...");
            foreach ($missingSections as $sectionKey) {
                $config = $defaultSections[$sectionKey];
                WebDesign::create([
                    'section_key' => $sectionKey,
                    'is_visible' => true,
                    'settings' => [],
                    'order' => $config['order'],
                    'status' => true,
                ]);
                $this->line("  ✅ Đã thêm: {$config['name']} ({$sectionKey})");
            }
        }

        // Hiển thị cảnh báo về sections thừa
        if (!empty($extraSections)) {
            $this->warn("⚠️  Phát hiện {count($extraSections)} sections không có trong config mặc định:");
            foreach ($extraSections as $sectionKey) {
                $this->line("  • {$sectionKey}");
            }
            $this->line("Bạn có thể xóa chúng thủ công nếu không cần thiết.");
        }

        // Clear cache
        app(WebDesignService::class)->clearCache();

        $this->info('✅ Đồng bộ hoàn tất!');
        return 0;
    }

    private function displayCheckResults(array $missingSections, array $extraSections, array $defaultSections): void
    {
        $this->info('📊 Kết quả kiểm tra WebDesign sections:');
        $this->newLine();

        if (empty($missingSections) && empty($extraSections)) {
            $this->info('✅ Tất cả sections đã được đồng bộ!');
            return;
        }

        if (!empty($missingSections)) {
            $this->warn("❌ Thiếu {count($missingSections)} sections:");
            foreach ($missingSections as $sectionKey) {
                $config = $defaultSections[$sectionKey];
                $this->line("  • {$config['name']} ({$sectionKey})");
            }
            $this->newLine();
        }

        if (!empty($extraSections)) {
            $this->warn("⚠️  Thừa {count($extraSections)} sections:");
            foreach ($extraSections as $sectionKey) {
                $this->line("  • {$sectionKey}");
            }
            $this->newLine();
        }

        $this->line('Chạy lệnh không có --check để thực hiện đồng bộ.');
    }
}
