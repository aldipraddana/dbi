<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GmailAccounts extends Model
{
    protected $table = 'gmail_accounts';

    protected $fillable = [
        'email',
        'password',
        'no_hp',
        'seri_hp',
        'keterangan',
        'details',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'details' => 'array',
    ];

     protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->created_by = auth()->user()->id ?? $model->created_by;
            $model->updated_by = auth()->user()->id ?? $model->updated_by;
        });
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
