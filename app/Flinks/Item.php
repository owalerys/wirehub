<?php

namespace App\Flinks;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsFlinks;
use App\Contracts\Item as ContractsItem;
use App\Services\Discovery;
use App\Services\Flinks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model implements ContractsItem
{
    use SoftDeletes, IsFlinks, HasUniversalIdentifier;

    protected $table = 'flinks_items';

    protected $hidden = ['login_id', 'username'];

    protected $fillable = [
        'login_id', 'username', 'last_refresh', 'is_scheduled_refresh', 'institution', 'error', 'type'
    ];

    public function getUniqueId(): string
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return Discovery::PROVIDER_FLINKS;
    }

    public function getModelType(): string
    {
        return Discovery::TYPE_ITEM;
    }

    public function getInstitutionName(): string
    {
        return $this->institution;
    }

    public function getInstitutionMeta(): ?object
    {
        return null;
    }

    public function disconnect()
    {
        /** @var Flinks $service */
        $service = app(Flinks::class);

        $service->deleteCard($this);
    }

    public function eagerRefresh()
    {
        /** @var Flinks $service */
        $service = app(Flinks::class);

        $service->startSession($this, false);
        $service->getAccountsDetail(false);
    }

    /**
     * To be run in an async job, get the summary first, then gets the full transaction payload
     */
    public function summaryRefresh()
    {
        /** @var Flinks $service */
        $service = app(Flinks::class);

        $service->startSession($this);
        $service->getAccountsSummary();
    }

    public function detailRefresh(bool $fullHistory = false)
    {
        /** @var Flinks $service */
        $service = app(Flinks::class);

        $service->startSession($this);
        $service->getAccountsDetail($fullHistory);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'item_id', 'id');
    }

    function canDelete(): bool
    {
        return true;
    }
}
