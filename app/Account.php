<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'external_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id', 'external_id');
    }
}
