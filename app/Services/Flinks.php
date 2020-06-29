<?php

namespace App\Services;

use App\Flinks\Account;
use App\Flinks\Item;
use App\Flinks\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Flinks
{
    /** @property string $requestId */
    private $requestId;
    /** @property Item $item */
    private $item;

    private $time;

    private function getClient()
    {
        return Http::withOptions([
            'base_uri' => $this->getBaseUrl() . '/v3/' . $this->getCustomerId() . '/BankingServices/'
        ]);
    }

    private function getBaseUrl()
    {
        return env('FLINKS_API_BASE');
    }

    private function getCustomerId()
    {
        return env('FLINKS_CUSTOMER_ID');
    }

    private function handleError(Response $response)
    {
        $statusCode = $response->status();
        $reason = $response['FlinksCode'];

        if ($statusCode === 400 && in_array($reason, ['INVALID_LOGIN'])) {
            $this->item->error = $response['FlinksCode'];
            $this->item->save();
        } elseif ($statusCode === 401 && in_array($reason, [
            'INVALID_USERNAME',
            'INVALID_PASSWORD',
            'INVALID_SECURITY_RESPONSE',
            'QUESTION_NOT_FOUND',
            'UNKNOWN_CHALLENGE_KEY',
            'DISABLED_LOGIN',
            'NEW_ACCOUNT',
            'SECURITYRESPONSES_INCOMPLETE'
        ])) {
            $this->item->error = $response['FlinksCode'];
            $this->item->save();
        }

        throw new \Exception($response);
    }

    private function startTimeout()
    {
        $this->time = time();
    }

    private function checkTimeout()
    {
        if (time() > $this->time + 30 * 60) {
            throw new \Exception('30 minute timeout.');
        }
    }

    public function startSession(Item $item, bool $cached = true)
    {
        $response = $this->getClient()->post('Authorize', [
            'LoginId' => $item->login_id,
            'MostRecentCached' => $cached,
            'Save' => !$cached
        ]);

        if (!$response->successful()) $this->handleError($response);

        $this->requestId = $response['RequestId'];
        $this->item = $item;
    }

    public function getAccountsSummary()
    {
        $this->startTimeout();

        $response = $this->getClient()->post('GetAccountsSummary', [
            'requestId' => $this->requestId,
            'WithBalance' => true
        ]);

        if (!$response->successful()) $this->handleError($response);

        if ($response->status() === 202) {
            $data = null;

            while ($data === null) {
                sleep(10);

                $data = $this->getAccountsSummaryPoll();
            }

            $accounts = $data['Accounts'];
            $login = $data['Login'];
        } else {
            $accounts = $response['Accounts'];
            $login = $response['Login'];
        }

        $this->persistApiData($login, $accounts);
    }

    private function persistApiData(array $login, array $accounts)
    {
        $this->item->username = $login['Username'];
        $this->item->is_scheduled_refresh = $login['IsScheduledRefresh'];
        $this->item->last_refresh = Carbon::parse($login['LastRefresh']);

        $this->item->save();

        foreach ($accounts as $account) {
            $createdAccount = Account::updateOrCreate([
                'external_id' => $account['Id']
            ], [
                'transit_number' => isset($account['TransitNumber']) ? $account['TransitNumber'] : null,
                'institution_number' => isset($account['InstitutionNumber']) ? $account['InstitutionNumber'] : null,
                'account_number' => $account['AccountNumber'],
                'currency' => $account['Currency'],
                'type' => isset($account['Type']) ? $account['Type'] : null,
                'category' => $account['Category'],
                'title' => $account['Title'],
                'balances' => [
                    'available' => isset($account['Balance']['Available']) ? $account['Balance']['Available'] : null,
                    'current' => isset($account['Balance']['Current']) ? $account['Balance']['Current'] : null,
                    'limit' => isset($account['Balance']['Limit']) ? $account['Balance']['Limit'] : null
                ],
                'item_id' => $this->item->id,
                'holder' => isset($account['Holder']) ? $account['Holder'] : null
            ]);

            if (isset($account['Transactions'])) {
                foreach ($account['Transactions'] as $transaction) {
                    $createdTransaction = Transaction::updateOrCreate([
                        'external_id' => $transaction['Id'],
                        'account_id' => $account['Id'],
                        'code' => $transaction['Code'],
                        'description' => $transaction['Description'],
                        'debit' => $transaction['Debit'],
                        'credit' => $transaction['Credit'],
                        'balance' => $transaction['Balance'],
                        'date' => $transaction['Date']
                    ]);
                }
            }
        }
    }

    private function getAccountsSummaryPoll(): ?\Illuminate\Http\Client\Response
    {
        $this->checkTimeout();

        $response = $this->getClient()->get('GetAccountsSummaryAsync/' . $this->requestId);

        if (!$response->successful()) $this->handleError($response);

        if ($response->status() === 202) return null;

        return $response;
    }

    public function getAccountsDetail($fullHistory = false)
    {
        $this->startTimeout();

        $response = $this->getClient()->post('GetAccountsDetail', [
            'requestId' => $this->requestId,
            'WithBalance' => true,
            'WithTransactions' => true,
            'DaysOfTransactions' => $fullHistory ? 'Days365' : 'Days90'
        ]);

        if (!$response->successful()) $this->handleError($response);

        if ($response->status() === 202) {
            $data = null;

            while ($data === null) {
                sleep(10);

                $data = $this->getAccountsDetailPoll();
            }

            $accounts = $data['Accounts'];
            $login = $data['Login'];
        } else {
            $accounts = $response['Accounts'];
            $login = $response['Login'];
        }

        $this->persistApiData($login, $accounts);
    }

    private function getAccountsDetailPoll(): ?\Illuminate\Http\Client\Response
    {
        $this->checkTimeout();

        $response = $this->getClient()->get('GetAccountsDetailAsync/' . $this->requestId);

        if (!$response->successful()) $this->handleError($response);

        if ($response->status() === 202) return null;

        return $response;
    }

    public function setScheduledRefresh(Item $item)
    {
        $response = $this->getClient()->patch('SetScheduledRefresh', [
            'loginId' => $item->login_id,
            'isActivated' => false,
        ]);

        if (!$response->successful()) $this->handleError($response);
    }

    public function deleteCard(Item $item)
    {
        $response = $this->getClient()->delete('DeleteCard/' . $item->login_id);

        if (!$response->successful()) $this->handleError($response);
    }
}
