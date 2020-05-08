<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function accounts()
    {
        return $this->belongsToMany(Account::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
