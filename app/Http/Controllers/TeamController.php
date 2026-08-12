<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount('players')->orderBy('name')->get();

        return view('manage.teams', compact('teams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:10',
        ]);

        Team::create($data);

        return back()->with('status', 'Team added.');
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return back()->with('status', 'Team deleted.');
    }
}
