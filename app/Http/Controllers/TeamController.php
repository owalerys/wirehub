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
        $team = Team::where('id', $teamId)->with(['users', 'accounts', 'accounts.item']);

        if (!$team) return response('', 404);

        return response()->json($team);
    }

    public function createTeam(Request $request, ServicesTeam $service)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'owner_name' => 'required|string',
            'email' => 'required|email|unique:users,email'
        ]);

        list($team, $owner) = $service->createTeam($request->input('name'), $request->input('contact_name'), $request->input('email'));

        return response()->json(['team' => $team, 'owner' => $owner]);
    }
}
