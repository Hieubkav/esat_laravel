<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitor;
use App\Models\PostView;
use App\Models\ProductView;

class ResetVisitorTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitor:reset {--type=all : Type to reset (all, visitors, content)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset visitor tracking data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        switch ($type) {
            case 'visitors':
                $this->resetVisitors();
                break;
            case 'content':
                $this->resetContent();
                break;
            case 'all':
            default:
                $this->resetAll();
                break;
        }

        return 0;
    }

    private function resetVisitors()
    {
        if ($this->confirm('Bạn có chắc chắn muốn xóa tất cả dữ liệu tracking lượt truy cập?')) {
            Visitor::resetAll();
            $this->info('✅ Đã reset dữ liệu tracking lượt truy cập thành công!');
        } else {
            $this->info('❌ Hủy bỏ reset dữ liệu.');
        }
    }

    private function resetContent()
    {
        if ($this->confirm('Bạn có chắc chắn muốn xóa tất cả dữ liệu tracking lượt xem nội dung?')) {
            PostView::resetAll();
            ProductView::resetAll();
            $this->info('✅ Đã reset dữ liệu tracking nội dung thành công!');
        } else {
            $this->info('❌ Hủy bỏ reset dữ liệu.');
        }
    }

    private function resetAll()
    {
        if ($this->confirm('Bạn có chắc chắn muốn xóa TẤT CẢ dữ liệu tracking?')) {
            Visitor::resetAll();
            PostView::resetAll();
            ProductView::resetAll();
            $this->info('✅ Đã reset tất cả dữ liệu tracking thành công!');
        } else {
            $this->info('❌ Hủy bỏ reset dữ liệu.');
        }
    }
}
