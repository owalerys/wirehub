<?php

namespace App\Services;

use App\Account;
use App\Item;
use App\Jobs\QueueTransactionNotification;
use App\Jobs\UpdateItem;
use App\Transaction;
use Illuminate\Support\Facades\DB;
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

    protected function getNotificationMap(Item $item)
    {
        $itemId = $item->external_id;
        $notificationMap = [];

        $query = DB::select("select t.external_id from transactions t
        inner join accounts a on a.external_id = t.account_id
        inner join items i on i.external_id = a.item_id
        where i.external_id = '$itemId'");

        $transactionIds = collect($query)->pluck('external_id')->all();

        foreach ($transactionIds as $trx) {
            $notificationMap[$trx] = true;
        }

        return $notificationMap;
    }

    public function getTransactions(Item $item, string $range = 'month', bool $notify = false)
    {
        if (!in_array($range, ['week', 'month', 'year'])) throw new \Exception("Invalid range: $range");

        $count = 500;
        $offset = 0;
        $total = 500;
        $fail = false;

        if ($notify) {
            $notificationMap = $this->getNotificationMap($item);
        }

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
                $externalId = $transaction['transaction_id'];

                Transaction::updateOrCreate([
                    'external_id' => $externalId,
                ], $transaction);

                if ($notify && !isset($notificationMap[$externalId])) QueueTransactionNotification::dispatch($externalId);
            }

            $offset += $count;
        }

        UpdateItem::dispatch($item->external_id);
    }

    public function forceRefreshTransactions(Item $item)
    {
        $response = $this->client->post('/transactions/refresh', $this->withAuthentication([
            'access_token' => $item->access_token,
        ]));

        if (!$response->ok()) {
            throw new \Exception($response);
        }
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
