<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengadaanStock extends Model
{
    use HasFactory;

    protected $table = 'pengadaan_stocks';

    protected $fillable = [
        'client_id',
        'tanggal_masuk',
        'supplier_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PengadaanStockDetail::class);
    }
}
