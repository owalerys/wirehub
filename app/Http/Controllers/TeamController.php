<?php

namespace App\Http\Controllers;

use App\Services\Team as ServicesTeam;
use App\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function getTeams()
    {
        $teams = Team::all();

        return response()->json($teams);
    }

    public function getTeam(int $teamId)
    {
        $team = Team::where('id', $teamId)->with(['users', 'accounts', 'accounts.item', 'users.roles'])->first();

        if (!$team) return response('', 404);

        return response()->json($team);
    }

    public function createTeam(Request $request, ServicesTeam $service)
    {
        $this->validate($request, [
            'organization' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'invite' => 'required|boolean'
        ]);

        list($team, $owner) = $service->createTeam($request->input('organization'), $request->input('name'), $request->input('email'));

        return response()->json(['team' => $team, 'owner' => $owner]);
    }
}
