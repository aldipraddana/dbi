<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class WifiOrders extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer',
        'no_handphone',
        'address',
        'wifi_speed',
        'bill',
        'ppn',
        'total',
        'payment_type',
        'payment_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (WifiOrders $model): void {
            $userId = auth()->id();
            $model->created_by = $userId;
            $model->updated_by = $userId;
            $model->bill = str_replace(['.', ','], '', $model->bill);
            $model->ppn = str_replace(['.', ','], '', $model->ppn);
        });

        static::updating(function (WifiOrders $model): void {
            $userId = auth()->id();
            $model->updated_by = $userId;
            $model->bill = str_replace(['.', ','], '', $model->bill);
            $model->ppn = str_replace(['.', ','], '', $model->ppn);
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
