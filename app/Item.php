<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $casts = [
        'available_products' => 'array',
        'billed_products' => 'array',
        'error' => 'object',
        'status' => 'object',
        'institution' => 'object'
    ];

    protected $hidden = ['access_token'];

    protected $fillable = [
        'status', 'external_id', 'access_token', 'institution', 'webhook', 'error', 'available_products', 'billed_products', 'consent_expiration_time'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class, 'item_id', 'external_id');
    }
}
