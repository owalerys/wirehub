<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $casts = [
        'category' => 'array',
        'location' => 'object',
        'payment_meta' => 'object'
    ];

    protected $fillable = [
        'external_id', 'account_id', 'category', 'category_id', 'transaction_type', 'name', 'amount',
        'iso_currency_code', 'uofficial_currency_code', 'date', 'authorized_date', 'location',
        'payment_meta', 'payment_channel', 'pending', 'pending_transaction_id', 'account_owner',
        'transaction_code'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'external_id');
    }
}
