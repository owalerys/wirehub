<?php

namespace App\Policies;

use App\Contracts\Account as ContractsAccount;
use App\Policies\Traits\UserHelper;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization, UserHelper;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $this->userIsAdmin($user);
    }

    public function viewOwn(User $user)
    {
        return $user->team_id !== null && $this->userIsTeamMember($user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user, ContractsAccount $account)
    {
        return $this->userIsAdmin($user)
            || ($this->userIsTeamMember($user)
                && in_array($user->team_id, $account->teams->pluck('id')->values()));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  ContractsAccount  $account
     * @return mixed
     */
    public function update(User $user)
    {
        return $this->userIsAdmin($user);
    }
}
