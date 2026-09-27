<?php

namespace App\Filament\Resources\PengadaanStockResource\Pages;

use App\Filament\Resources\PengadaanStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengadaanStocks extends ListRecords
{
    public static string $resource = PengadaanStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
