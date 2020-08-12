<?php

namespace App\Policies;

use App\Contracts\Item;
use App\Policies\Traits\UserHelper;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization, UserHelper;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Contracts\Item  $item
     * @return mixed
     */
    public function view(User $user, Item $item)
    {
        return $this->userIsAdmin($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $this->userIsAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Contracts\Item  $item
     * @return mixed
     */
    public function delete(User $user, Item $item)
    {
        return $item->canDelete() && $this->userIsAdmin($user);
    }
}
