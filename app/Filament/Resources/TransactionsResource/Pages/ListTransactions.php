<?php

namespace App\Filament\Resources\TransactionsResource\Pages;

use App\Filament\Resources\TransactionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Transaksi'),
        ];
    }

    public function getHeading(): string
    {
        return __('Data Transaksi');
    }

    public function getFooter(): ?View
    {
        $data['menu'] = 'transaction';
        return view('filament.resources.transactions-resource.widgets.filter-export-excel', $data);
    }
    
}
