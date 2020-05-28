<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

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

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withTimestamps()->wherePivot('deleted_at', null);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id', 'external_id');
    }
}
