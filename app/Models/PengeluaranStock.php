<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengeluaranStock extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_stocks';

    protected $fillable = [
        'client_id',
        'tanggal_keluar',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PengeluaranStockDetail::class);
    }
}
