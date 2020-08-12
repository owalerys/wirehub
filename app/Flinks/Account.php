<?php

namespace App\Flinks;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsFlinks;
use App\Contracts\Account as ContractsAccount;
use App\Services\Discovery;
use App\Team;
use App\Teamable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model implements ContractsAccount
{
    use SoftDeletes, IsFlinks, HasUniversalIdentifier;

    protected $table = 'flinks_accounts';

    protected $fillable = [
        'external_id', 'item_id', 'balances', 'title', 'transit_number', 'account_number', 'institution_number', 'type', 'category', 'overdraft_limit', 'holder', 'currency'
    ];

    protected $casts = [
        'balances' => 'object',
        'holder' => 'object'
    ];

    public function getUniqueId(): string
    {
        return $this->external_id;
    }

    public function getProvider(): string
    {
        return Discovery::PROVIDER_FLINKS;
    }

    public function getModelType(): string
    {
        return Discovery::TYPE_ACCOUNT;
    }

    public function getName(): string
    {
        return $this->title;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
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
        $eft = [
            'branch' => $this->transit_number,
            'account' => $this->account_number,
            'institution' => $this->institution_number
        ];

        return (object) [
            'eft' => (object) $eft
        ];
    }

    public function getCurrencyCode(): string
    {
        return $this->currency;
    }

    public function getParentResourceIdentifier(): string
    {
        return Discovery::createIdentifier(
            Discovery::PROVIDER_FLINKS,
            Discovery::TYPE_ITEM,
            $this->item_id
        );
    }

    public function getMask(): string
    {
        return substr($this->account_number, -4);
    }

    public function isDepository(): bool
    {
        return $this->category === 'Operations';
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
        return $this->hasMany(Transaction::class, 'account_id', 'external_id');
    }

    public function hasWireMeta(): bool
    {
        return false;
    }

    function hasAccountMeta(): bool
    {
        return true;
    }
}
