<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanaMasuk extends Model
{
    use HasFactory;

    protected $table = 'dana_masuks';

    protected $fillable = [
        'nomor',
        'tanggal_masuk',
        'no_invoice',
        'customer',
        'po',
        'item',
        'jumlah_dana_masuk',
        'tipe_pembayaran',
        'status_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'jumlah_dana_masuk' => 'decimal:2',
        ];
    }
}
