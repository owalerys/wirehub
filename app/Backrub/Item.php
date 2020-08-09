<?php

namespace App\Backrub;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsBackrub;
use App\Contracts\Item as ContractsItem;
use App\Services\Discovery;
use App\Services\Backrub;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model implements ContractsItem
{
    use SoftDeletes, IsBackrub, HasUniversalIdentifier;

    protected $table = 'backrub_items';

    protected $hidden = ['password', 'email'];

    protected $fillable = [
        'external_id', 'email', 'password', 'name', 'error'
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
        return Discovery::TYPE_ITEM;
    }

    public function getInstitutionName(): string
    {
        // TODO make dynamic and grab from accounts instead
        return 'BMO';
    }

    public function getInstitutionMeta(): ?object
    {
        return null;
    }

    public function disconnect()
    {
        $this->delete();
    }

    public function eagerRefresh()
    {
        // TODO implement refresh service
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
        // TODO implement refresh service
        /** @var Flinks $service */
        $service = app(Flinks::class);

        $service->startSession($this);
        $service->getAccountsSummary();
    }

    public function detailRefresh(bool $fullHistory = false)
    {
        // TODO implement refresh service
        /** @var Flinks $service */
        $service = app(Flinks::class);

        $service->startSession($this);
        $service->getAccountsDetail($fullHistory);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'item_id', 'id');
    }
}
