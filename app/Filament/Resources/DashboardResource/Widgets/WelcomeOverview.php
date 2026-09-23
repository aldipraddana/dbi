<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WelcomeOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Selamat datang', ucfirst(strtolower(auth()->user()->name))),
            Stat::make('Hari Ini', now()->format('d M Y')),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}
