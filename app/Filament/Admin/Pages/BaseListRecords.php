<?php

namespace App\Filament\Admin\Pages;

use Filament\Resources\Pages\ListRecords;

abstract class BaseListRecords extends ListRecords
{
    // Tối ưu pagination - hiển thị ít records hơn để load nhanh
    protected string|int|null $defaultTableRecordsPerPageSelectOption = 10;
    
    // Có thể override trong từng class con nếu cần
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 25, 50];
    }
}
