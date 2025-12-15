<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Visitor;
use App\Models\PostView;
use App\Models\ProductView;
use App\Constants\NavigationGroups;
use App\Traits\HasPageRoleBasedAccess;

class VisitorAnalytics extends Page
{
    use HasPageRoleBasedAccess;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.admin.pages.visitor-analytics';
    protected static ?string $navigationGroup = NavigationGroups::WEBSITE_SETTINGS;
    protected static ?string $title = 'Phân tích lượt truy cập';
    protected static ?string $navigationLabel = 'Phân tích truy cập';
    protected static ?int $navigationSort = 45;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetVisitors')
                ->label('Reset dữ liệu truy cập')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Xác nhận reset dữ liệu truy cập')
                ->modalDescription('Bạn có chắc chắn muốn xóa tất cả dữ liệu tracking lượt truy cập website? Hành động này không thể hoàn tác.')
                ->action(function () {
                    Visitor::resetAll();
                    
                    Notification::make()
                        ->title('Đã reset dữ liệu tracking truy cập thành công')
                        ->success()
                        ->send();
                }),

            Action::make('resetContent')
                ->label('Reset dữ liệu nội dung')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Xác nhận reset dữ liệu nội dung')
                ->modalDescription('Bạn có chắc chắn muốn xóa tất cả dữ liệu tracking lượt xem bài viết và sản phẩm?')
                ->action(function () {
                    PostView::resetAll();
                    ProductView::resetAll();
                    
                    Notification::make()
                        ->title('Đã reset dữ liệu tracking nội dung thành công')
                        ->success()
                        ->send();
                }),

            Action::make('resetAll')
                ->label('Reset tất cả')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Xác nhận reset tất cả dữ liệu')
                ->modalDescription('Bạn có chắc chắn muốn xóa TẤT CẢ dữ liệu tracking? Hành động này không thể hoàn tác.')
                ->action(function () {
                    Visitor::resetAll();
                    PostView::resetAll();
                    ProductView::resetAll();
                    
                    Notification::make()
                        ->title('Đã reset tất cả dữ liệu tracking thành công')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\VisitorStatsWidget::class,
            \App\Filament\Admin\Widgets\TopContentWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getWidgetData(): array
    {
        return [];
    }

    public function getVisibleWidgets(): array
    {
        return $this->getWidgets();
    }
}
