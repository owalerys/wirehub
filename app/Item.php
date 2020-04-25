<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function accounts()
    {
        return $this->hasMany(Account::class, 'item_id', 'external_id');
    }
}
