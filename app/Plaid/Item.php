<?php

namespace App\Plaid;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsPlaid;
use App\Contracts\Item as ContractsItem;
use App\Services\Discovery;
use App\Services\Plaid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model implements ContractsItem
{
    use SoftDeletes, IsPlaid, HasUniversalIdentifier;

    protected $table = 'plaid_items';

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
        return Discovery::TYPE_ITEM;
    }

    public function getInstitutionName(): string
    {
        return $this->institution->name;
    }

    public function getInstitutionMeta(): ?object
    {
        return $this->institution;
    }

    public function disconnect()
    {
        /** @var Plaid $service */
        $service = app(Plaid::class);

        $service->removeItem($this);
    }

    public function eagerRefresh()
    {
        /** @var Plaid $service */
        $service = app(Plaid::class);

        $service->forceRefreshTransactions($this);
    }

    public function summaryRefresh()
    {
        /** @var Plaid $service */
        $service = app(Plaid::class);

        $service->getItem($this->access_token);
    }

    public function detailRefresh(bool $fullHistory = false)
    {
        /** @var Plaid $service */
        $service = app(Plaid::class);

        $service->getTransactions($this, $fullHistory ? 'year' : 'month');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'item_id', 'external_id');
    }
}
