<?php

namespace App\Http\Controllers;

use App\Services\Team as ServicesTeam;
use App\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function getTeams()
    {
        $this->authorize('viewAny', Team::class);

        $teams = Team::with('users')->get();

        return response()->json($teams);
    }

    public function getTeam(int $teamId)
    {
        $team = Team::where('id', $teamId)->with(['accounts' => function ($query) {
            $query->where('type', 'depository');
            $query->with('item');
        }])->with(['users.roles'])->first();

        if (!$team) return response('', 404);

        $this->authorize('view', $team);

        return response()->json($team);
    }

    public function createTeam(Request $request, ServicesTeam $service)
    {
        $this->authorize('create', Team::class);

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
