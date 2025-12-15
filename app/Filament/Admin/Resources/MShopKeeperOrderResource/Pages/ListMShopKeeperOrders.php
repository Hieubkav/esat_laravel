<?php

namespace App\Filament\Admin\Resources\MShopKeeperOrderResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperOrderResource;
use App\Services\MShopKeeperService;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ListMShopKeeperOrders extends ListRecords
{
    protected static string $resource = MShopKeeperOrderResource::class;
    
    protected static ?string $title = 'Đơn đặt hàng MShopKeeper';

    protected function getHeaderActions(): array
    {
        return [
            // Actions đã được định nghĩa trong Resource
        ];
    }

    /**
     * Override để lấy dữ liệu từ API thay vì database
     */
    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        try {
            $mshopkeeperService = app(MShopKeeperService::class);
            
            // Lấy parameters từ request
            $page = request()->get('page', 1);
            $perPage = request()->get('per_page', 25);
            $search = request()->get('search', '');
            $sortField = request()->get('sort', 'OrderDate');
            $sortDirection = request()->get('direction', 'desc');
            
            // Gọi API để lấy orders
            $result = $mshopkeeperService->getOrders([
                'Page' => $page,
                'Limit' => $perPage,
                'SortField' => $sortField,
                'SortType' => $sortDirection === 'desc' ? 0 : 1,
                'SearchText' => $search,
            ]);

            if ($result['success']) {
                $orders = collect($result['data'] ?? []);
                
                // Transform data để phù hợp với table
                $transformedOrders = $orders->map(function ($order) {
                    return [
                        'OrderId' => $order['OrderId'] ?? '',
                        'OrderNo' => $order['OrderNo'] ?? '',
                        'OrderDate' => $order['OrderDate'] ?? '',
                        'TotalAmount' => $order['TotalAmount'] ?? 0,
                        'Status' => $order['Status'] ?? 'Pending',
                        'Description' => $order['Description'] ?? '',
                        'Customer' => [
                            'Name' => $order['Customer']['Name'] ?? '',
                            'Tel' => $order['Customer']['Tel'] ?? '',
                            'Email' => $order['Customer']['Email'] ?? '',
                            'Address' => $order['Customer']['Address'] ?? '',
                        ],
                        'OrderDetails' => $order['OrderDetails'] ?? [],
                    ];
                });

                return $transformedOrders;
            } else {
                // Hiển thị thông báo lỗi
                Notification::make()
                    ->title('Lỗi kết nối MShopKeeper API')
                    ->body($result['error']['message'] ?? 'Không thể lấy dữ liệu đơn hàng')
                    ->danger()
                    ->send();

                return collect([]);
            }

        } catch (\Exception $e) {
            // Log lỗi và hiển thị thông báo
            Log::error('Error fetching MShopKeeper orders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->title('Lỗi hệ thống')
                ->body('Có lỗi xảy ra khi lấy dữ liệu đơn hàng: ' . $e->getMessage())
                ->danger()
                ->send();

            return collect([]);
        }
    }

    /**
     * Override để disable pagination mặc định
     */
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100];
    }

    /**
     * Thêm thông tin hướng dẫn
     */
    protected function getHeaderWidgets(): array
    {
        return [
            // Có thể thêm widgets thống kê ở đây
        ];
    }
}
