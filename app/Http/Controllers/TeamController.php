<?php

namespace App\Http\Controllers;

use App\Http\Resources\Team as ResourcesTeam;
use App\Services\Team as ServicesTeam;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    public function getTeams()
    {
        Gate::authorize('view-any-teams');

        $teams = Team::with('users')->get();

        return response()->json(ResourcesTeam::collection($teams->values()));
    }

    public function getTeam(int $teamId)
    {
        $team = Team::find($teamId);

        if (!$team) return abort(404);

        Gate::authorize('view-team', $team);

        $team->getAllAccounts();

        $team->load('users');

        return response()->json(new ResourcesTeam($team));
    }

    public function createTeam(Request $request, ServicesTeam $service)
    {
        Gate::authorize('create-team');

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
