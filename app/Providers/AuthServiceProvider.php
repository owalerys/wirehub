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
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-any-accounts', AccountPolicy::class . '@viewAny');
        Gate::define('view-own-accounts', AccountPolicy::class . '@viewOwn');
        Gate::define('view-account', AccountPolicy::class . '@view');
        Gate::define('update-account', AccountPolicy::class . '@update');

        Gate::define('view-item', ItemPolicy::class . '@view');
        Gate::define('create-item', ItemPolicy::class . '@create');
        Gate::define('delete-item', ItemPolicy::class . '@delete');

        Gate::define('view-team', TeamPolicy::class . '@view');
        Gate::define('view-any-teams', TeamPolicy::class . '@viewAny');
        Gate::define('create-team', TeamPolicy::class . '@create');

        Gate::define('confirm-transaction', TransactionPolicy::class . '@confirm');
    }
}
