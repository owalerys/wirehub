<?php

namespace App\Backrub;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsBackrub;
use App\Contracts\Account as ContractsAccount;
use App\Services\Discovery;
use App\Team;
use App\Teamable;
use Illuminate\Database\Eloquent\Model;

class Account extends Model implements ContractsAccount
{
    use IsBackrub, HasUniversalIdentifier;

    protected $table = 'backrub_accounts';

    protected $fillable = [
        'external_id',
        'name',
        'nickname',
        'institution',
        'item_id'
    ];

    public function getUniqueId(): string
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return Discovery::PROVIDER_BACKRUB;
    }

    public function getModelType(): string
    {
        return Discovery::TYPE_ACCOUNT;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function getType(): ?string
    {
        return 'Wire Deposit';
    }

    public function getBalances(): object
    {
        return (object)[];
    }

    public function getNumbers(): object
    {
        return (object)[];
    }

    public function getCurrencyCode(): string
    {
        // TODO detect currency from transactions
        return 'USD';
    }

    public function getParentResourceIdentifier(): string
    {
        return Discovery::createIdentifier(
            Discovery::PROVIDER_BACKRUB,
            Discovery::TYPE_ITEM,
            $this->item_id
        );
    }

    public function getMask(): string
    {
        return '';
    }

    public function isDepository(): bool
    {
        return true;
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function teams()
    {
        return $this->morphToMany(Team::class, 'teamable')->using(Teamable::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id', 'id');
    }
}
