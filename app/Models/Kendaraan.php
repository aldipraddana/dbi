<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kendaraans';

    protected $fillable = [
        'no_polisi',
        'jenis_kendaraan',
        'merk',
        'model_tipe',
        'tahun_pembuatan',
        'warna',
        'no_rangka',
        'no_mesin',
        'kapasitas',
        'status_kepemilikan',
        'tanggal_berlaku_stnk',
        'tanggal_berlaku_pajak',
        'kondisi',
        'foto_kendaraan',
        'karyawan_id',
        'catatan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_berlaku_stnk' => 'date',
            'tanggal_berlaku_pajak' => 'date',
        ];
    }

    protected static function booted(): void
    {
        parent::booted();
        static::creating(function (Kendaraan $model) {
            $userId = auth()->id();
            $model->created_by = $userId;
            $model->updated_by = $userId;
        });
        static::updating(function (Kendaraan $model) {
            $model->updated_by = auth()->id();
        });
        static::deleting(function (Kendaraan $model) {
            if ($model->isForceDeleting()) {
                return;
            }
            $model->deleted_by = auth()->id();
            $model->saveQuietly();
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
