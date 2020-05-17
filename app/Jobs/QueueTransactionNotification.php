<?php

namespace App\Jobs;

use App\Notifications\NewTransaction;
use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueTransactionNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get notifiable user
        $transaction = Transaction::where('external_id', $this->transactionId)->has('account.teams.users')->with('account.teams.users')->first();

        // Filter out accounts with no team and amounts above 0 (outgoing)
        if ($transaction === null || $transaction->amount > 0) return;

        $toNotify = $transaction->account->teams->first()->users->first();

        $toNotify->notify(new NewTransaction($transaction));
    }
}
