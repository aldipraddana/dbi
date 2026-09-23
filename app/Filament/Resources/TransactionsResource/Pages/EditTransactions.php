<?php

namespace App\Filament\Resources\TransactionsResource\Pages;

use App\Filament\Resources\TransactionsResource;
use Filament\Resources\Pages\EditRecord;

class EditTransactions extends EditRecord
{
    protected static string $resource = TransactionsResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('view', ['record' => $this->record]);
    // }

    public function getHeading(): string
    {
        return __('Edit Data Transaksi Produk');
    }
}
