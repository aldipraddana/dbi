<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'karyawans';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'foto',
        'jenis_kelamin',
        'tanggal_lahir',
        'no_telepon',
        'email',
        'alamat',
        'kota',
        'jabatan',
        'departemen',
        'tanggal_masuk_kerja',
        'status_kepegawaian',
        'status_aktif',
        'no_ktp',
        'dokumen_pendukung',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk_kerja' => 'date',
            'status_aktif' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        parent::booted();
        static::creating(function (Karyawan $model) {
            $userId = auth()->id();
            $model->created_by = $userId;
            $model->updated_by = $userId;
        });
        static::updating(function (Karyawan $model) {
            $model->updated_by = auth()->id();
        });
        static::deleting(function (Karyawan $model) {
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

    public function kendaraans(): HasMany
    {
        return $this->hasMany(Kendaraan::class, 'karyawan_id');
    }
}
