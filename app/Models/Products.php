<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    protected $fillable = [
        'serial_number',
        'imei1',
        'name',
        'type',
        'is_active',
        'description',
        'date_of_entry',
        'date_of_purchase',
        'joki',
        'joki_name',
        'joki_nominal',
        'price',
        'color_memory',
        'marketplace',
        'booking_number',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->serial_number = $model->serial_number ?? '-';
            $model->joki_nominal = $model->joki_nominal ? str_replace(['.', ','], [''], $model->joki_nominal) : null;
            $model->price = str_replace(['.', ','], [''], $model->price);
        });

        static::creating(function ($model) {
            $model->created_by = auth()->user()->id ?? null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user()->id ?? $model->created_by;
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id ?? $model->created_by;
            $model->save();
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'created_by', ownerKey: 'id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'updated_by', ownerKey: 'id');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'deleted_by', ownerKey: 'id');
    }
}
