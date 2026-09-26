<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'kode_client',
        'nama_client',
        'jenis_client',
        'no_telepon',
        'email',
        'alamat',
        'kota',
        'npwp',
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
        static::creating(function (Client $model) {
            $userId = auth()->id();
            $model->created_by = $userId;
            $model->updated_by = $userId;
        });
        static::updating(function (Client $model) {
            $model->updated_by = auth()->id();
        });
        static::deleting(function (Client $model) {
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
