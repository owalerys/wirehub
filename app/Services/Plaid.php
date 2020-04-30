<?php

namespace App\Services;

use App\Account;
use App\Item;
use Illuminate\Support\Facades\Http;
use Jose\Component\Core\JWK;

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

    private function getPublicKey()
    {
        return env('PLAID_PUBLIC_KEY');
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

    public function exchangePublicToken(string $publicToken): ?Item
    {
        $response = $this->client->post('/item/public_token/exchange', $this->withAuthentication(
            [
                'public_token' => $publicToken
            ]
        ));

        if (!$response->ok()) throw new \Exception($response);

        $item = $this->getItem($response['access_token']);

        $this->getAccounts($item);

        return $item;
    }

    public function getWebhookVerificationKey(string $keyId): JWK
    {
        $response = $this->client->post('/webhook_verification_key/get', $this->withAuthentication([
            'key_id' => $keyId
        ]));

        if (!$response->ok()) throw new \Exception($response);

        return new JWK($response['key']);
    }

    private function getItem(string $accessToken): Item
    {
        $response = $this->client->post('/item/get', $this->withAuthentication([
            'access_token' => $accessToken
        ]));

        $item = Item::updateOrCreate(
            [ 'external_id' => $response['item']['item_id'] ],
            array_merge(
                $response['item'],
                [
                    'external_id' => $response['item']['item_id'],
                    'access_token' => $accessToken,
                    'status' => $response['status']
                ]
            )
        );

        if (!$item->institution) {
            $institutionResponse = $this->client->post('/institutions/get_by_id', [
                'institution_id' => $response['item']['institution_id'],
                'public_key' => $this->getPublicKey(),
                'options' => [
                    'include_optional_metadata' => true
                ]
            ]);

            $item->institution = $institutionResponse['institution'];

            $item->save();
        }

        return $item;
    }

    private function getAccounts(Item $item)
    {
        // EXTRACT
        $response = $this->client->post('/auth/get', $this->withAuthentication([
            'access_token' => $item->access_token
        ]));

        if (!$response->ok()) throw new \Exception($response);

        // TRANSFORM
        $accounts = [];

        foreach ($response['accounts'] as $account) {
            $accounts[$account['account_id']] = $account;
            $accounts[$account['account_id']]['numbers'] = [];
        }

        foreach ($response['numbers'] as $type => $numbers) {
            foreach ($numbers as $numberEntity) {
                $accounts[$numberEntity['account_id']]['numbers'][$type] = $numberEntity;
            }
        }

        // LOAD
        foreach ($accounts as $account) {
            $account = Account::updateOrCreate(
                [ 'external_id' => $account['account_id'] ],
                array_merge(
                    $account,
                    [
                        'external_id' => $account['account_id'],
                        'item_id' => $item->external_id
                    ]
                )
            );
        }
    }
}
