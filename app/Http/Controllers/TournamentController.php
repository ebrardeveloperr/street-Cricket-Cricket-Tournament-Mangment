<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::orderBy('name')->get();

        return view('manage.tournaments', compact('tournaments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        Tournament::create($data);

        return back()->with('status', 'Tournament added.');
    }

    public function destroy(Tournament $tournament)
    {
        $tournament->delete();

        return back()->with('status', 'Tournament deleted.');
    }
}
