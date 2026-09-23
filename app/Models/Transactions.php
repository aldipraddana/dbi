<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @mixin IdeHelperTransactions
 */
class Transactions extends Model
{
    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'customer',
        'no_handphone',
        'address',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Transactions $model): void {
            DB::transaction(function () use ($model): void {
                $userId = auth()->id();
                $model->created_by = $userId;
                $model->updated_by = $userId;
            });
        });

        static::updating(function (Transactions $model): void {
            $userId = auth()->id();
            $model->updated_by = $userId;
        });
    }

    public function productTransactions(): HasMany
    {
        return $this->hasMany(ProductTransactions::class, 'transaction_id');
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
