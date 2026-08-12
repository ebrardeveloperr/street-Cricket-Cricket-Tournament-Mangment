<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inning;
use Illuminate\Http\Request;

class InningApiController extends Controller
{
    // Starts the 2nd innings (teams swapped, target set).
    public function store(Request $request)
    {
        $data = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'innings_number' => 'required|integer',
            'batting_team_id' => 'required|exists:teams,id',
            'bowling_team_id' => 'required|exists:teams,id',
            'target' => 'nullable|integer',
        ]);

        $inning = Inning::create($data);

        return response()->json(['inning_id' => $inning->id], 201);
    }

    // Closes out an innings with its final totals.
    public function update(Request $request, Inning $inning)
    {
        $data = $request->validate([
            'total_runs' => 'required|integer',
            'total_wickets' => 'required|integer',
            'total_balls' => 'required|integer',
            'extras' => 'required|integer',
            'status' => 'required|string',
        ]);

        $inning->update($data);

        return response()->json(['ok' => true]);
    }
}
