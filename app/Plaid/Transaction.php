<?php

namespace App\Plaid;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsPlaid;
use App\Contracts\Transaction as ContractsTransaction;
use App\Services\Discovery;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model implements ContractsTransaction
{
    use SoftDeletes, IsPlaid, HasUniversalIdentifier;

    protected $table = 'plaid_transactions';

    protected $casts = [
        'category' => 'array',
        'location' => 'object',
        'payment_meta' => 'object'
    ];

    protected $fillable = [
        'external_id', 'account_id', 'category', 'category_id', 'transaction_type', 'name', 'amount',
        'iso_currency_code', 'uofficial_currency_code', 'date', 'authorized_date', 'location',
        'payment_meta', 'payment_channel', 'pending', 'pending_transaction_id', 'account_owner',
        'transaction_code'
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
        return Discovery::TYPE_TRANSACTION;
    }

    public function getNumericalID(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): string
    {
        return (string) $this->amount * -1;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getParentResourceIdentifier(): string
    {
        return Discovery::createIdentifier(
            Discovery::PROVIDER_PLAID,
            Discovery::TYPE_ACCOUNT,
            $this->account_id
        );
    }

    public function getBalance(): ?string
    {
        return null;
    }

    public function getCategory(): ?array
    {
        return $this->category;
    }

    public function getTransactionType(): ?string
    {
        return $this->transaction_type;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->iso_currency_code;
    }

    public function getPending(): ?bool
    {
        return (bool) $this->pending;
    }

    public function getAuthorizedDate(): ?string
    {
        return $this->authorized_date;
    }

    public function getLocation(): ?object
    {
        return $this->location;
    }

    public function getPaymentMeta(): ?object
    {
        return $this->payment_meta;
    }

    public function getPaymentChannel(): ?string
    {
        return $this->payment_channel;
    }

    public function getTransactionCode(): ?string
    {
        return $this->transaction_code ? (string) $this->transacton_code : null;
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

        $query->where('name', 'like', '%wire%');
        $query->orWhere('name', 'like', '%transfer%');
        $query->orWhere('name', 'like', '%trnsfr%');
        $query->orWhere('name', 'like', '%deposit%');

        return $query;
    }
}
