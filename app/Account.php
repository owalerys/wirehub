<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'external_id', 'item_id', 'balances', 'name', 'numbers', 'mask', 'official_name', 'type', 'subtype', 'verification_status'
    ];

    protected $casts = [
        'balances' => 'object',
        'numbers' => 'object'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'external_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id', 'external_id');
    }
}
