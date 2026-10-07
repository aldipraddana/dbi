<?php

namespace App\Filament\Resources\DanaKeluarResource\Pages;

use App\Filament\Resources\DanaKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDanaKeluars extends ListRecords
{
    protected static string $resource = DanaKeluarResource::class;

    public function getTitle(): string
    {
        return 'Dana Keluar';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Dana Keluar'),
        ];
    }
}
