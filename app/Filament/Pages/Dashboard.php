<?php

namespace App\Filament\Pages;

use App\Filament\Resources\DashboardResource\Widgets\TransactionOverview;
use App\Filament\Resources\DashboardResource\Widgets\WelcomeOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getWidgets(): array
    {
        return [
            WelcomeOverview::class
        ];
    }

}