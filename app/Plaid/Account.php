<?php

namespace App\Plaid;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsPlaid;
use App\Contracts\Account as ContractsAccount;
use App\Services\Discovery;
use App\Team;
use App\Teamable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model implements ContractsAccount
{
    use SoftDeletes, IsPlaid, HasUniversalIdentifier;

    protected $table = 'plaid_accounts';

    protected $fillable = [
        'external_id', 'item_id', 'balances', 'name', 'numbers', 'mask', 'official_name', 'type', 'subtype', 'verification_status'
    ];

    protected $casts = [
        'balances' => 'object',
        'numbers' => 'object'
    ];

    public function getUniqueId(): string
    {
        return $this->external_id;
    }

    public function getProvider(): string
    {
        return Discovery::PROVIDER_PLAID;
    }

    public function getModelType(): string
    {
        return Discovery::TYPE_ACCOUNT;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getBalances(): object
    {
        return $this->balances;
    }

    public function getNumbers(): object
    {
        return $this->numbers ?: (object) [];
    }

    public function getCurrencyCode(): string
    {
        return $this->balances->iso_currency_code;
    }

    public function getParentResourceIdentifier(): string
    {
        return Discovery::createIdentifier(
            Discovery::PROVIDER_PLAID,
            Discovery::TYPE_ITEM,
            $this->item_id
        );
    }

    public function getMask(): string
    {
        return $this->mask;
    }

    public function isDepository(): bool
    {
        return $this->type === 'depository';
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'external_id');
    }

    public function teams()
    {
        return $this->morphToMany(Team::class, 'teamable')->using(Teamable::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id', 'external_id');
    }
}
