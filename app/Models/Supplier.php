<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'jenis_barang_jasa',
        'no_telepon',
        'email',
        'alamat',
        'kota',
        'npwp',
        'nama_bank',
        'no_rekening',
        'atas_nama_rekening',
        'status',
        'catatan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        parent::booted();
        static::creating(function (Supplier $model) {
            $userId = auth()->id();
            $model->created_by = $userId;
            $model->updated_by = $userId;
        });
        static::updating(function (Supplier $model) {
            $model->updated_by = auth()->id();
        });
        static::deleting(function (Supplier $model) {
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
}
