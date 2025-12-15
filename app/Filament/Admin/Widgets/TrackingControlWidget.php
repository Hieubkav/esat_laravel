<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use App\Models\Visitor;
use App\Models\PostView;
use App\Models\ProductView;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class TrackingControlWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.tracking-control';
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '5s';

    public function getViewData(): array
    {
        return [
            'totalVisitors' => Visitor::count(),
            'totalPostViews' => PostView::count(),
            'totalProductViews' => ProductView::count(),
            'todayVisitors' => Visitor::whereDate('visited_at', today())->count(),
            'todayPostViews' => PostView::whereDate('viewed_at', today())->count(),
            'todayProductViews' => ProductView::whereDate('viewed_at', today())->count(),
        ];
    }

    public function resetVisitors()
    {
        Visitor::resetAll();

        Notification::make()
            ->title('✅ Đã reset dữ liệu lượt truy cập thành công!')
            ->body('Tất cả dữ liệu tracking lượt truy cập website đã được xóa.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public function resetContentViews()
    {
        PostView::resetAll();
        ProductView::resetAll();

        Notification::make()
            ->title('✅ Đã reset dữ liệu lượt xem nội dung thành công!')
            ->body('Tất cả dữ liệu tracking lượt xem bài viết và sản phẩm đã được xóa.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public function resetAllTracking()
    {
        Visitor::resetAll();
        PostView::resetAll();
        ProductView::resetAll();

        Notification::make()
            ->title('✅ Đã reset TẤT CẢ dữ liệu tracking thành công!')
            ->body('Tất cả dữ liệu tracking đã được xóa hoàn toàn. Bạn có thể bắt đầu test từ đầu.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public function generateTestData()
    {
        Artisan::call('visitor:generate-test-data', ['--count' => 30]);

        Notification::make()
            ->title('✅ Đã tạo dữ liệu test thành công!')
            ->body('30 bản ghi dữ liệu test đã được tạo. Hãy kiểm tra các widget để xem kết quả.')
            ->success()
            ->duration(5000)
            ->send();
    }
}
