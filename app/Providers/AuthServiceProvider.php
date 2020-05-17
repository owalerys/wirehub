<?php

namespace App\Providers;

use App\Account;
use App\Item;
use App\Policies\AccountPolicy;
use App\Policies\ItemPolicy;
use App\Policies\TeamPolicy;
use App\Policies\TransactionPolicy;
use App\Team;
use App\Transaction;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Account::class => AccountPolicy::class,
        Item::class => ItemPolicy::class,
        Team::class => TeamPolicy::class,
        Transaction::class => TransactionPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
