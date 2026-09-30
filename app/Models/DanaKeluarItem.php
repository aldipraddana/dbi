<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DanaKeluarItem extends Model
{
    use HasFactory;

    protected $table = 'dana_keluar_items';

    protected $fillable = [
        'dana_keluar_id',
        'pengadaan_stock_id',
        'nama_barang',
        'supplier_id',
        'qty',
        'harga_satuan',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:4',
            'harga_satuan' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function danaKeluar(): BelongsTo
    {
        return $this->belongsTo(DanaKeluar::class);
    }

    public function pengadaanStock(): BelongsTo
    {
        return $this->belongsTo(PengadaanStock::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
