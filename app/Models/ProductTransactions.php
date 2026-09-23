<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTransactions extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'description',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->price = (int) preg_replace('/\D/', '', $model->price);
            $model->subtotal = (int) preg_replace('/\D/', '', $model->subtotal);
            $model->created_by = auth()->user()->id ?? $model->created_by;
            $model->updated_by = auth()->user()->id ?? $model->updated_by;

            //update products status
            $product = Products::find($model->product_id);
            if ($product) {
                $product->is_active = false;
                $product->save();
            }
        });
    }

    public function transactions(): BelongsTo
    {
        return $this->belongsTo(related: Transactions::class, foreignKey: 'transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(related: Products::class, foreignKey: 'product_id', ownerKey: 'id');
    }

}
