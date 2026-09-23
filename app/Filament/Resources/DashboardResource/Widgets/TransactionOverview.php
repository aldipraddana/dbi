<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Tenants;
use App\Models\TransactionBills;
use App\Models\TransactionPayments;
use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPendapatan = TransactionPayments::sum('nominal');
        $totalPenyewa = Tenants::count();
        $totalPenyewaBelumBayar = Transactions::where('payment_status', 'UNPAID')->count();
        return [
            Stat::make('Total Pendapatan', 'Rp'.number_format($totalPendapatan, 0, ',', '.')),
            Stat::make('Total Penyewa', $totalPenyewa),
            Stat::make('Total Penyewa Belum Bayar', $totalPenyewaBelumBayar),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
