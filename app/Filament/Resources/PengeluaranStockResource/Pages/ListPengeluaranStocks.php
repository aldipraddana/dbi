<?php

namespace App\Filament\Resources\PengeluaranStockResource\Pages;

use App\Filament\Resources\PengeluaranStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengeluaranStocks extends ListRecords
{
    public static string $resource = PengeluaranStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
