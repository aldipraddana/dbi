<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WifiCustomers extends Model
{
    protected $fillable = [
        'customer',
        'no_handphone',
        'address',
        'wifi_speed',
        'bill',
        'ppn',
        'created_by',
        'updated_by',
    ];

     protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->bill = str_replace(['.', ','], '', $model->bill);
            $model->ppn = str_replace(['.', ','], '', $model->ppn);
            $model->created_by = auth()->user()->id ?? $model->created_by;
            $model->updated_by = auth()->user()->id ?? $model->updated_by;
        });
    }
}
    