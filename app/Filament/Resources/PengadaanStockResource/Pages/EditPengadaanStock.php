<?php

namespace App\Filament\Resources\PengadaanStockResource\Pages;

use App\Filament\Resources\PengadaanStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengadaanStock extends EditRecord
{
    public static string $resource = PengadaanStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
