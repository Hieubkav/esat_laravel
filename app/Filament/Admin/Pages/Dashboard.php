<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [];
    }
}
