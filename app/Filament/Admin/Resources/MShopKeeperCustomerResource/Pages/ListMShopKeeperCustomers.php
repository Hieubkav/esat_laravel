<?php

namespace App\Filament\Admin\Resources\MShopKeeperCustomerResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperCustomerResource;
use App\Services\MShopKeeperService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Log;

class ListMShopKeeperCustomers extends ListRecords
{
    protected static string $resource = MShopKeeperCustomerResource::class;

    protected array $memberLevels = [];
    protected ?string $searchQuery = null;

    public function mount(): void
    {
        parent::mount();

        // Load member levels for filter
        $this->loadMemberLevels();
    }

    protected function loadMemberLevels(): void
    {
        try {
            // Tạm thời disable để test
            $this->memberLevels = [];
            return;

            // Kiểm tra class tồn tại trước khi resolve
            $serviceClass = 'App\\Services\\MShopKeeperService';
            if (!class_exists($serviceClass)) {
                Log::error('MShopKeeperService class does not exist');
                return;
            }

            $service = app($serviceClass);

            if (!$service) {
                Log::error('Failed to resolve MShopKeeperService from container');
                return;
            }

            if (!method_exists($service, 'isInitialized') || !$service->isInitialized()) {
                Log::error('MShopKeeperService is not properly initialized');
                return;
            }

            $result = $service->getMemberLevels(1, 100);

            if ($result['success'] && isset($result['data']['member_levels'])) {
                foreach ($result['data']['member_levels'] as $level) {
                    $this->memberLevels[$level['MemberLevelID']] = $level['MemberLevelName'];
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error loading member levels in ListMShopKeeperCustomers', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Set empty array as fallback
            $this->memberLevels = [];
        }
    }

    /**
     * Override getTableQuery để sử dụng database query
     */
    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Models\MShopKeeperCustomer::query();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sync từ API')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    try {
                        // Chạy sync command
                        \Illuminate\Support\Facades\Artisan::call('mshopkeeper:sync-customers');

                        $output = \Illuminate\Support\Facades\Artisan::output();

                        // Parse output để lấy stats
                        preg_match('/Created\s*\|\s*(\d+)/', $output, $createdMatches);
                        preg_match('/Updated\s*\|\s*(\d+)/', $output, $updatedMatches);

                        $created = $createdMatches[1] ?? 0;
                        $updated = $updatedMatches[1] ?? 0;

                        \Filament\Notifications\Notification::make()
                            ->title('Sync thành công!')
                            ->body("Đã tạo mới: {$created}, Cập nhật: {$updated}")
                            ->success()
                            ->send();

                        // Refresh trang
                        $this->redirect(request()->header('Referer'));

                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Sync thất bại!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Xác nhận sync dữ liệu')
                ->modalDescription('Bạn có chắc chắn muốn sync dữ liệu khách hàng từ MShopKeeper API?')
                ->modalSubmitActionLabel('Sync ngay'),
            Actions\Action::make('search_customer')
                ->label('Tìm kiếm khách hàng')
                ->icon('heroicon-o-magnifying-glass')
                ->color('primary')
                ->form([
                    TextInput::make('search_query')
                        ->label('Tìm kiếm theo SĐT hoặc Email')
                        ->placeholder('Nhập số điện thoại hoặc email...')
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Redirect với search query
                    return redirect()->to(
                        request()->url() . '?tableSearch=' . urlencode($data['search_query'])
                    );
                }),

            Actions\Action::make('member_levels')
                ->label('Hạng thẻ thành viên')
                ->icon('heroicon-o-star')
                ->color('secondary')
                ->modalHeading('Danh sách hạng thẻ thành viên')
                ->modalContent(fn (): \Illuminate\Contracts\View\View => view(
                    'filament.admin.resources.mshopkeeper-customer.member-levels-modal',
                    ['memberLevels' => $this->memberLevels]
                ))
                ->modalWidth('2xl'),

            Actions\Action::make('customer_guide')
                ->label('Hướng dẫn')
                ->icon('heroicon-o-question-mark-circle')
                ->color('info')
                ->modalHeading('Hướng dẫn quản lý khách hàng')
                ->modalContent(fn (): \Illuminate\Contracts\View\View => view(
                    'filament.admin.resources.mshopkeeper-customer.customer-guide'
                ))
                ->modalWidth('2xl'),
        ];
    }

    public function getTitle(): string
    {
        return 'Khách hàng MShopKeeper';
    }

    public function getHeading(): string
    {
        return 'Khách hàng MShopKeeper';
    }
}
