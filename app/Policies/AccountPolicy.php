<?php

namespace App\Policies;

use App\Account;
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
        return $this->userIsAdmin($user) || ($user->team_id !== null && $this->userIsTeamMember($user));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Account  $account
     * @return mixed
     */
    public function view(User $user, Account $account)
    {
        return $this->userIsAdmin($user)
            || ($this->userIsTeamMember($user)
                && in_array($user->team_id, $account->teams->pluck('id')->all()));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Account  $account
     * @return mixed
     */
    public function update(User $user, Account $account)
    {
        return $this->userIsAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Account  $account
     * @return mixed
     */
    public function delete(User $user, Account $account)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Account  $account
     * @return mixed
     */
    public function restore(User $user, Account $account)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Account  $account
     * @return mixed
     */
    public function forceDelete(User $user, Account $account)
    {
        //
    }
}
