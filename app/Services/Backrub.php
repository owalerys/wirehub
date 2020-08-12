<?php

namespace App\Services;

use App\Backrub\Account;
use App\Backrub\Item;
use App\Backrub\Transaction;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class Backrub
{
    /** @property string $accessToken */
    private $accessToken;

    /** @property Item $item */
    private $item;

    private function getClient()
    {
        $client = Http::withOptions([
            'base_uri' => $this->getBaseUrl() . '/api/'
        ]);

        if ($this->accessToken) {
            $client = $client->withHeaders([
                'Access-Token' => $this->accessToken
            ]);
        }

        return $client;
    }

    private function getBaseUrl()
    {
        return env('BACKRUB_API_BASE');
    }

    private function handleError(Response $response)
    {
        throw new \Exception($response);
    }

    public function connectItem(string $username, string $password): Item
    {
        $params = [
            'username' => $username,
            'password' => $password
        ];

        $response = $this->getClient()->asForm()->post('authenticate', $params);

        if (!$response->successful() || $response['invalid'] === true) throw new Error($response);

        $item = Item::updateOrCreate([
            'external_id' => $response['user']['id'],
            'username' => $username
        ], [
            'password' => Crypt::encrypt($password),
            'name' => $response['user']['name']
        ]);

        return $item;
    }

    public function startSession(Item $item)
    {
        $params = [
            'username' => $item->username,
            'password' => Crypt::decrypt($item->password)
        ];

        $response = $this->getClient()->asForm()->post('authenticate', $params);

        if (!$response->successful() || $response['invalid'] === true) $this->handleError($response);

        $this->item = $item;

        $this->accessToken = $response['token']['token'];
    }

    public function syncAccounts(Item $item, bool $fullHistory = false)
    {
        $this->startSession($item);

        $response = $this->getClient()->get('bankAccount');

        if (!$response->successful()) $this->handleError($response);

        $accounts = $response['bankAccount'];

        foreach ($accounts as $account) {
            $accountRecord = Account::updateOrCreate([
                'external_id' => $account['id'],
                'item_id' => $item->id
            ], [
                'name' => $account['customerId'],
                'institution' => $account['bank']
            ]);

            $this->syncTransactions($accountRecord, $fullHistory);
        }
    }

    public function syncTransactions(Account $account, bool $fullHistory = false)
    {
        $now = Carbon::now();

        $daysToGoBack = $fullHistory === true ? 60 : 7;

        $now->subDays($daysToGoBack);

        $dateString = $now->toIso8601String();

        $response = $this->getClient()->get('transaction', [
            'transaction.bankAccountId' => $account->external_id,
            'transaction.postedAt' => ">$dateString"
        ]);

        if (!$response->successful()) $this->handleError($response);

        $transactions = $response['transaction'];

        foreach ($transactions as $transaction) {
            $transactionRecord = Transaction::updateOrCreate([
                'external_id' => $transaction['id'],
                'account_id' => $account->id
            ], [
                'sender_reference_number' => $transaction['senderReferenceNumber'],
                'amount' => $transaction['amount'],
                'sender_address' => $transaction['senderAddress'],
                'sender_name' => $transaction['senderName'],
                'receiver_bank_account_number' => $transaction['receiverBankAccountNumber'],
                'receiver_reference_number' => $transaction['receiverReferenceNumber'],
                'date' => new Carbon($transaction['postedAt']),
                'receiver_name' => $transaction['receiverName'],
                'currency' => $transaction['currency'],
            ]);
        }
    }
}
