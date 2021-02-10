<?php

namespace App\Backrub;

use App\Concerns\HasUniversalIdentifier;
use App\Concerns\IsBackrub;
use App\Contracts\Item as ContractsItem;
use App\Services\Discovery;
use App\Services\Backrub;
use Illuminate\Database\Eloquent\Model;

class Item extends Model implements ContractsItem
{
    use IsBackrub, HasUniversalIdentifier;

    protected $table = 'backrub_items';

    protected $hidden = ['password', 'username'];

    protected $fillable = [
        'external_id', 'username', 'password', 'name', 'error'
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
        // TODO handle backrub disconnect
    }

    public function eagerRefresh()
    {
        /** @var Backrub $service */
        $service = app(Backrub::class);

        $service->syncAccounts($this);
    }

    /**
     * To be run in an async job, get the summary first, then gets the full transaction payload
     */
    public function summaryRefresh()
    {
        return;
    }

    public function detailRefresh(bool $fullHistory = false)
    {
        /** @var Backrub $service */
        $service = app(Backrub::class);

        $service->syncAccounts($this, $fullHistory);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'item_id', 'id');
    }

    function canDelete(): bool
    {
        return false;
    }
}
