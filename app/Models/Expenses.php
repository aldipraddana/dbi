<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expenses extends Model
{
    protected $fillable = [
        'expense_date',
        'expense_time',
        'payment_type',
        'amount',
        'title',
        'category',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Expenses $model): void {
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });

        static::updating(function (Expenses $model): void {
            $model->updated_by = auth()->id();
        });

        static::saving(function (Expenses $model): void {
            if (is_string($model->amount)) {
                $model->amount = (int) preg_replace('/\D/', '', $model->amount);
            }
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
}
