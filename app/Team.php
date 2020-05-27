<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function accounts()
    {
        return $this->belongsToMany(Account::class)->withTimestamps()->wherePivot('deleted_at', null);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
