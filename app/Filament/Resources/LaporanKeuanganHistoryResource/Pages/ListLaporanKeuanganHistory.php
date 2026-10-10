<?php

namespace App\Filament\Resources\LaporanKeuanganHistoryResource\Pages;

use App\Filament\Resources\LaporanKeuanganHistoryResource;
use App\Filament\Resources\LaporanKeuanganHistoryResource\LaporanKeuanganHistoryResource as ResourcesLaporanKeuanganHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanKeuanganHistory extends ListRecords
{
    protected static string $resource = ResourcesLaporanKeuanganHistoryResource::class;
}
