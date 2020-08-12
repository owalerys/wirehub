<?php

namespace App\Http\Resources;

use App\Contracts\Item as ContractsItem;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ContractsItem $resource
 */
class Item extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $output = [
            'id' => $this->getResourceIdentifier(),
            'institution' => $this->getInstitutionName(),
            'institution_meta' => $this->getInstitutionMeta(),
            'accounts' => Account::collection($this->whenLoaded('accounts')),
            'deletable' => $this->canDelete()
        ];

        return $output;
    }
}
