<?php

namespace App\Filament\Resources\PengeluaranStockResource\Pages;

use App\Filament\Resources\PengeluaranStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengeluaranStock extends ViewRecord
{
    public static string $resource = PengeluaranStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
