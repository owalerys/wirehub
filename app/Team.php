<?php

namespace App;

use App\Plaid\Account as PlaidAccount;
use App\Flinks\Account as FlinksAccount;
use App\Backrub\Account as BackrubAccount;
use App\Services\Discovery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Team extends Model
{
    public $allAccounts;

    public function plaidAccounts()
    {
        return $this->morphedByMany(PlaidAccount::class, 'teamable')->using(Teamable::class);
    }

    public function flinksAccounts()
    {
        return $this->morphedByMany(FlinksAccount::class, 'teamable')->using(Teamable::class);
    }

    public function backrubAccounts()
    {
        return $this->morphedByMany(BackrubAccount::class, 'teamable')->using(Teamable::class);
    }

    public function getAllAccounts(): Collection
    {
        $collection = collect();

        $this->load('plaidAccounts.item');
        $this->load('flinksAccounts.item');
        $this->load('backrubAccounts.item');

        $collection = $collection->merge(Discovery::keyUniversal($this->plaidAccounts));
        $collection = $collection->merge(Discovery::keyUniversal($this->flinksAccounts));
        $collection = $collection->merge(Discovery::keyUniversal($this->backrubAccounts));

        $this->allAccounts = $collection;

        return $collection;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
