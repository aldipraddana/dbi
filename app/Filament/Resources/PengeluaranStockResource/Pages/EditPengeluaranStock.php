<?php

namespace App\Filament\Resources\PengeluaranStockResource\Pages;

use App\Filament\Resources\PengeluaranStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengeluaranStock extends EditRecord
{
    public static string $resource = PengeluaranStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
