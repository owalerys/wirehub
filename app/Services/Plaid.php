<?php

namespace App\Services;

use App\Account;
use App\Item;
use App\Jobs\UpdateItem;
use App\Transaction;
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

    public function getItem(string $accessToken): Item
    {
        $response = $this->client->post('/item/get', $this->withAuthentication([
            'access_token' => $accessToken
        ]));

        if (!$response->ok()) throw new \Exception($response);

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

    public function getAccounts(Item $item)
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

    public function getTransactions(Item $item, string $range)
    {
        if (!in_array($range, ['week', 'month', 'year'])) throw new \Exception("Invalid range: $range");

        $count = 500;
        $offset = 0;
        $total = 500;
        $fail = false;

        while ($offset < $total && !$fail) {
            $response = $this->client->post('/transactions/get', $this->withAuthentication([
                'access_token' => $item->access_token,
                'start_date' => date('Y-m-d', strtotime("-1 $range")),
                'end_date' => date('Y-m-d', strtotime('+1 day')),
                'options' => [
                    'count' => $count,
                    'offset' => $offset
                ]
            ]));

            if (!$response->ok()) {
                $fail = true;
                throw new \Exception($response);
            }

            $total = (int) $response['total_transactions'];

            foreach ($response['transactions'] as $transaction) {
                Transaction::updateOrCreate([
                    'external_id' => $transaction['transaction_id'],
                ], $transaction);
            }

            $offset += $count;
        }

        UpdateItem::dispatch($item->external_id);
    }

    public function removeTransactions(array $transactionIds)
    {
        $transactions = Transaction::whereIn('external_id', $transactionIds)->all();

        foreach ($transactions as $transaction) {
            $transaction->delete();
        }

        return;
    }
}
