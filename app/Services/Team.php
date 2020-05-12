<?php

namespace App\Services;

use App\Notifications\UserTeamInvite;
use App\Team as AppTeam;
use App\User;
use Illuminate\Support\Facades\Hash;

class Team
{
    public function createTeam($name, $contactName, $contactEmail, $invite = true)
    {
        $team = new AppTeam;

        $team->name = $name;

        $team->save();

        $owner = new User;

        function generateRandomString($length = 10)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        $owner->name = $contactName;
        $owner->email = $contactEmail;
        $owner->team_id = $team->id;
        $owner->password = Hash::make(generateRandomString(36));

        $owner->save();

        $owner->assignRole('owner');

        if ($invite) $this->inviteMember($owner, $team);

        return [ $team, $owner ];
    }

    public function inviteMember(User $user, AppTeam $team)
    {
        $token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);

        $user->notify(new UserTeamInvite($token, $team));
    }
}
