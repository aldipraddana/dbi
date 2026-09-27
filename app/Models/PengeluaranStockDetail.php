<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengeluaranStockDetail extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_stock_details';

    protected $fillable = [
        'pengeluaran_stock_id',
        'pengadaan_stock_detail_id',
        'qty',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
        ];
    }

    public function pengeluaranStock(): BelongsTo
    {
        return $this->belongsTo(PengeluaranStock::class);
    }

    public function pengadaanStockDetail(): BelongsTo
    {
        return $this->belongsTo(PengadaanStockDetail::class);
    }
}
