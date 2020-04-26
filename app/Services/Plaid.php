<?php

namespace App\Services;

use App\Item;
use Illuminate\Support\Facades\Http;

class Plaid
{
    private $client;

    public function __construct()
    {
        $this->client = Http::withOptions([
            'base_uri' => $this->getBaseUrl()
        ]);
    }

    private function getBaseUrl()
    {
        return env('PLAID_API_BASE');
    }

    private function getClientId()
    {
        return env('PLAID_CLIENT_ID');
    }

    private function getSecret()
    {
        return env('PLAID_SECRET');
    }

    private function withAuthentication(array $params)
    {
        return array_merge(
            [
                'client_id' => $this->getClientId(),
                'secret' => $this->getSecret()
            ],
            $params
        );
    }

    public function exchangePublicToken(string $publicToken)
    {
        $response = $this->client->post('/item/public_token/exchange', $this->withAuthentication(
            [
                'public_token' => $publicToken
            ]
        ));

        if (!$response->ok()) return;

        return $item = $this->getLink($response['access_token']);
    }

    private function getLink(string $accessToken): Item
    {
        $response = $this->client->post('/item/get', $this->withAuthentication([
            'access_token' => $accessToken
        ]));

        return Item::updateOrCreate(
            [ 'external_id' => $response['item']['item_id'] ],
            $response['item']
        );
    }
}
