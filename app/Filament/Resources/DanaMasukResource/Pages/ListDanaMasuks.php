<?php

namespace App\Filament\Resources\DanaMasukResource\Pages;

use App\Filament\Resources\DanaMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDanaMasuks extends ListRecords
{
    public static string $resource = DanaMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
