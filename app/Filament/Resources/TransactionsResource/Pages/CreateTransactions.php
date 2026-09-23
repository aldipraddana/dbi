<?php

namespace App\Filament\Resources\TransactionsResource\Pages;

use App\Filament\Resources\TransactionsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactions extends CreateRecord
{
    protected static string $resource = TransactionsResource::class;

    public function getHeading(): string
    {
        return __('Tambah Data Transaksi Produk');
    }
}
