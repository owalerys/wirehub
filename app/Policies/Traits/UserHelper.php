<?php

namespace App\Policies\Traits;

use App\User;

trait UserHelper {

    protected function userIsAdmin(User $user)
    {
        return $user->hasRole(['admin', 'super-admin']) && $user->active;
    }

    protected function userIsTeamMember(User $user)
    {
        return $user->hasRole(['member', 'owner']) && $user->active;
    }

}
