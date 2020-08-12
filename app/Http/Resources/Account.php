<?php

namespace App\Http\Resources;

use App\Contracts\Account as ContractsAccount;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ContractsAccount $resource
 */
class Account extends JsonResource
{
    public function __construct(ContractsAccount $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getResourceIdentifier(),
            'name' => $this->getNickname() ?: $this->getName(),
            'type' => $this->getType(),
            'numbers' => $this->getNumbers(),
            'balances' => $this->getBalances(),
            'mask' => $this->getMask(),
            'currency_code' => $this->getCurrencyCode(),
            'is_depository' => $this->isDepository(),
            'parent_id' => $this->getParentResourceIdentifier(),
            'teams' => Team::collection($this->whenLoaded('teams')),
            'transactions' => Transaction::collection($this->whenLoaded('transactions')),
            'item' => new Item($this->whenLoaded('item')),
            'has_account_meta' => $this->hasAccountMeta(),
            'has_wire_meta' => $this->hasWireMeta()
        ];
    }
}
