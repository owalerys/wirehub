<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Team extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'users' => User::collection($this->whenLoaded('users')),
            'accounts' => $this->when($this->allAccounts !== null, Account::collection($this->allAccounts ? $this->allAccounts->values() : collect()))
        ];
    }
}
