<?php

namespace App\Filament\Resources\PengadaanStockResource\Pages;

use App\Filament\Resources\PengadaanStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengadaanStock extends ViewRecord
{
    public static string $resource = PengadaanStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
