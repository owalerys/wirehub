<?php

namespace App\Services;

use App\Backrub\Account;
use App\Backrub\Item;
use App\Backrub\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Process\Process;

class Backrub
{

    public function connectItem(string $username): Item
    {
        $password = $username;

        $item = Item::updateOrCreate([
            'username' => $username,
            'external_id' => 0,
        ], [
            'password' => Crypt::encrypt($password),
            'name' => $username,
        ]);

        $account = Account::updateOrCreate([
            'item_id' => $item->id,
            'external_id' => 0,
        ], [
            'name' => env('SCRAPER_BMO_CUSTOMER_ID'),
            'institution' => 'BMO'
        ]);

        return $item;
    }

    public function syncAccounts(Item $item, bool $fullHistory = false)
    {
        $accounts = $item->accounts;

        foreach ($accounts as $account) {
            $this->syncTransactions($account, $fullHistory);
        }
    }

    public function syncTransactions(Account $account, bool $fullHistory = false)
    {
        $output = $this->runScrape();

        $total = count($output['transactionSummary']);

        for ($i = 0; $i < $total; $i++) {
            $transactionSummary = $output['transactionSummary'][$i];
            $transactionDetail = $output['transactionDetail'][$i];

            $transactionRecord = Transaction::updateOrCreate([
                'sender_reference_number' => $transactionDetail['sendersReference'],
                'receiver_reference_number' => $transactionDetail['referenceNumber'],
                'account_id' => $account->id
            ], [
                'external_id' => 0,
                'amount' => str_replace(',', '', $transactionSummary['amount']),
                'sender_address' => $transactionDetail['orderingCustomerAddress'],
                'sender_name' => $transactionSummary['orderingCustomerName'],
                'receiver_bank_account_number' => $transactionSummary['beneficiaryAccountNumber'],
                'date' => new Carbon($transactionDetail['postedAt']),
                'receiver_name' => $transactionSummary['beneficiaryName'],
                'currency' => $transactionSummary['currency'],
            ]);
        }
    }

    private function runScrape(): array
    {
        $process = new Process(['node', 'scrape/bmo.js']);
        // Set a generous timeout of 4 mins because this process can run quite long on a cheap prod server...
        $process->setTimeout(240);

        $exitCode = $process->run();

        $output = $process->getOutput();

        if ($exitCode > 0) {
            throw new \Exception('Scraper failed: ' . $output);
        }

        $decoded = json_decode($output, true);

        $summaryCount = count($decoded['transactionSummary']);
        $detailCount = count($decoded['transactionDetail']);
        if ($summaryCount === 0 || $summaryCount !== $detailCount) {
            throw new \Exception('[' . $summaryCount . ', ' . $detailCount . '] results came back from scraper run. ' . $output);
        }

        return $decoded;
    }
}
