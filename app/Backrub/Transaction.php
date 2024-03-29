<?php

namespace App\Backrub;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsBackrub;
use App\Contracts\Transaction as ContractsTransaction;
use App\Services\Discovery;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model implements ContractsTransaction
{
    use IsBackrub, HasUniversalIdentifier;

    protected $table = 'backrub_transactions';

    protected $fillable = [
        'external_id',
        'account_id',
        'date',
        'amount',
        'currency',
        'receiver_reference_number',
        'sender_name',
        'sender_address',
        'sender_reference_number',
        'receiver_name',
        'receiver_bank_account_number',
        'confirmed',
        'confirmed_at'
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
        return Discovery::TYPE_TRANSACTION;
    }

    public function getNumericalID(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return 'Wire ' . $this->receiver_reference_number;
    }

    public function getAmount(): string
    {
        return (string) $this->amount;
    }

    public function getDate(): string
    {
        $date = new Carbon($this->date);

        return $date->format('Y-m-d');
    }

    public function getParentResourceIdentifier(): string
    {
        return Discovery::createIdentifier(
            Discovery::PROVIDER_BACKRUB,
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
        return null;
    }

    public function getTransactionType(): ?string
    {
        return null;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currency;
    }

    public function getPending(): ?bool
    {
        return false;
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
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function scopeWire($query, User $user)
    {
        if ($user->hasRole(['admin', 'super-admin'])) return $query;

        return $query;
    }

    public function getWireMeta(): ?object
    {
        return (object) [
            'receiver' => (object) [
                'reference_number' => $this->receiver_reference_number,
                'name' => $this->receiver_name,
                'bank_account_number' => $this->receiver_bank_account_number
            ],
            'sender' => (object) [
                'name' => $this->sender_name,
                'address' => $this->sender_address,
                'reference_number' => $this->sender_reference_number
            ]
        ];
    }
}
