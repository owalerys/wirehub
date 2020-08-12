<?php

namespace App\Http\Resources;

use App\Contracts\Transaction as ContractsTransaction;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ContractsTransaction $resource
 */
class Transaction extends JsonResource
{
    public function __construct(ContractsTransaction $resource)
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
            'internal_id' => $this->getNumericalID(),
            'name' => $this->getName(),
            'amount' => $this->getAmount(),
            'date' => $this->getDate(),
            'balance' => $this->getBalance(),
            'category' => $this->getCategory(),
            'transaction_type' => $this->getTransactionType(),
            'currency_code' => $this->getCurrencyCode(),
            'pending' => $this->getPending(),
            'authorized_date' => $this->getAuthorizedDate(),
            'location' => $this->getLocation(),
            'payment_meta' => $this->getPaymentMeta(),
            'payment_channel' => $this->getPaymentChannel(),
            'transaction_code' => $this->getTransactionCode(),
            'confirmed' => $this->getConfirmed(),
            'confirmed_at' => $this->getConfirmedAt(),
            'parent_id' => $this->getParentResourceIdentifier(),
            'wire_meta' => $this->getWireMeta()
        ];
    }
}
