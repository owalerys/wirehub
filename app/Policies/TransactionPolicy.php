<?php

namespace App\Policies;

use App\Policies\Traits\UserHelper;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization, UserHelper;

    /**
     * Determine whether the user can confirm a transaction.
     *
     * @param  \App\User  $user
     * @param  \App\Plaid\Transaction|\App\Contracts\Transaction $transaction
     * @return mixed
     */
    public function confirm(User $user, $transaction)
    {
        return $this->userIsTeamMember($user) && in_array($user->team_id, $transaction->account->teams->pluck('id')->all());
    }
}
