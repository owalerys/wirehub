<?php

namespace App\Flinks;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsFlinks;
use App\Contracts\Transaction as ContractsTransaction;
use App\Services\Discovery;
use App\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model implements ContractsTransaction
{
    use SoftDeletes, IsFlinks, HasUniversalIdentifier;

    protected $table = 'flinks_transactions';

    protected $fillable = [
        'external_id', 'account_id', 'code', 'description', 'debit', 'credit', 'balance', 'date'
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
        return Discovery::TYPE_TRANSACTION;
    }

    public function getNumericalID(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->description;
    }

    public function getAmount(): string
    {
        if ($this->debit !== null) return (string) $this->debit * -1;
        if ($this->credit !== null) return (string) $this->credit;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getParentResourceIdentifier(): string
    {
        return Discovery::createIdentifier(
            Discovery::PROVIDER_FLINKS,
            Discovery::TYPE_ACCOUNT,
            $this->account_id
        );
    }

    public function getBalance(): ?string
    {
        return (string) $this->balance;
    }

    public function getCategory(): ?array
    {
        return null;
    }

    public function getTransactionType(): ?string
    {
        return null;
    }

    public function getCurrencyCode(): ?string
    {
        return null;
    }

    public function getPending(): ?bool
    {
        return null;
    }

    public function getAuthorizedDate(): ?string
    {
        return null;
    }

    public function getLocation(): ?object
    {
        return null;
    }

    public function getPaymentMeta(): ?object
    {
        return null;
    }

    public function getPaymentChannel(): ?string
    {
        return null;
    }

    public function getTransactionCode(): ?string
    {
        return null;
    }

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function getConfirmedAt(): ?string
    {
        return $this->confirmed_at;
    }

    public function confirm(bool $value)
    {
        $this->confirmed = (bool) $value;
        $this->confirmed_at = now();

        $this->save();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'external_id');
    }

    public function scopeWire($query, User $user)
    {
        if ($user->hasRole(['admin', 'super-admin'])) return $query;

        $query->where('debit', null);
        $query->where(function (EloquentBuilder $query) {
            $query->where('description', 'like', '%wire%');
            $query->orWhere('description', 'like', '%transfer%');
            $query->orWhere('description', 'like', '%trnsfr%');
            $query->orWhere('description', 'like', '%deposit%');
            $query->orWhere('description', 'like', '%cheque%');
            $query->orWhere('code', '=', 'CK');
        });

        return $query;
    }

    function getWireMeta(): ?object
    {
        return null;
    }
}
