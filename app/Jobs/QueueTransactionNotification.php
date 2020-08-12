<?php

namespace App\Jobs;

use App\Contracts\Transaction as ContractsTransaction;
use App\Notifications\NewTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class QueueTransactionNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @property ContractsTransaction|Model $transaction */
    protected $transaction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ContractsTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get notifiable user
        $this->transaction->load('account.teams.users');

        /** @var App\Contracts\Account $account */
        $account = $this->transaction->account;
        if (!$account->isDepository()) return;

        // No team associated
        if ($account->teams->count() === 0) return;

        if ($account->teams->first()->users->count() === 0) return;

        if (!$account->isDepository()) return;

        $toNotify = $this->transaction->account->teams->first()->users->first();

        if (stripos($toNotify->email, 'delete') !== false) return;

        $toNotify->notify(new NewTransaction($this->transaction));
    }
}
