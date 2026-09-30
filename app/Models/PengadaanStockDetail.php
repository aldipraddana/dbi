<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengadaanStockDetail extends Model
{
    use HasFactory;

    protected $table = 'pengadaan_stock_details';

    protected $fillable = [
        'pengadaan_stock_id',
        'nama_barang',
        'tipe',
        'pm',
        'qty',
        'harga_satuan',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'harga_satuan' => 'decimal:2',
        ];
    }

    public function pengadaanStock(): BelongsTo
    {
        return $this->belongsTo(PengadaanStock::class);
    }

    public function pengeluaranDetails(): HasMany
    {
        return $this->hasMany(PengeluaranStockDetail::class, 'pengadaan_stock_detail_id');
    }

    public function getAvailableQty(): int
    {
        $allocated = $this->pengeluaranDetails()->sum('qty');
        return max(0, $this->qty - $allocated);
    }
}
