<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Hash;

class Team
{
    public function createTeam($name, $contactName, $contactEmail)
    {
        $team = new Team;

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

        // TODO send password reset email to account owner
        return [ $team, $owner ];
    }
}
