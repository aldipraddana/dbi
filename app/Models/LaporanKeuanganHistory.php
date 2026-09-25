<?php

namespace App\Models;

use App\Enums\JenisLaporanEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKeuanganHistory extends Model
{
    use HasFactory;

    protected $table = 'laporan_keuangan_histories';

    protected $fillable = [
        'user_id',
        'jenis_laporan',
        'tanggal_mulai',
        'tanggal_akhir',
        'format',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->user()->id ?? null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user()->id ?? $model->created_by;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getJenisLaporanLabelAttribute(): string
    {
        return JenisLaporanEnum::tryFrom($this->jenis_laporan)?->label() ?? $this->jenis_laporan;
    }

    public function getPeriodeAttribute(): string
    {
        return $this->tanggal_mulai->format('d-m-Y') . ' s/d ' . $this->tanggal_akhir->format('d-m-Y');
    }
}
