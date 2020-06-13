<?php

namespace App\Observers;

use App\Contracts\Transaction;
use App\Jobs\QueueTransactionNotification;
use DateTime;

class TransactionObserver
{
    public function created(Transaction $transaction)
    {
        $today = new DateTime('now');

        $transactionDate = date_create($transaction->getDate());
        $transactionDate->setTime(0, 0);

        $intervalDays = $today->diff($transactionDate)->days;

        // If positive amount in last 7 days, notify.
        if ($intervalDays >= -7 && $transaction->getAmount() > 0) {
            QueueTransactionNotification::dispatch($transaction);
        }
    }
}
