<?php

namespace App\Providers;

use App\Flinks\Transaction as FlinksTransaction;
use App\Observers\TransactionObserver;
use App\Plaid\Transaction as PlaidTransaction;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        PlaidTransaction::observe(TransactionObserver::class);
        FlinksTransaction::observe(TransactionObserver::class);
    }
}
