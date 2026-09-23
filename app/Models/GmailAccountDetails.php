<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GmailAccountDetails extends Model
{
    protected $table = 'gmail_account_details';

    protected $fillable = [
        'gmail_account_id',
        'application_name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function gmailAccount()
    {
        return $this->belongsTo(GmailAccounts::class, 'gmail_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
