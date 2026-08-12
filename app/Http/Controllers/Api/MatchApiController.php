<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CricketMatch;
use App\Models\Inning;
use Illuminate\Http\Request;

class MatchApiController extends Controller
{
    // Creates a new match plus its first innings. Returns both IDs.
    public function store(Request $request)
    {
        $data = $request->validate([
            'tournament_id' => 'nullable|exists:tournaments,id',
            'team1_id' => 'required|exists:teams,id', // batting first
            'team2_id' => 'required|exists:teams,id',
            'total_overs' => 'required|integer|min:1',
        ]);

        $match = CricketMatch::create([
            'tournament_id' => $data['tournament_id'] ?? null,
            'team1_id' => $data['team1_id'],
            'team2_id' => $data['team2_id'],
            'total_overs' => $data['total_overs'],
            'status' => 'live',
            'match_date' => now()->toDateString(),
        ]);

        $inning = Inning::create([
            'match_id' => $match->id,
            'innings_number' => 1,
            'batting_team_id' => $data['team1_id'],
            'bowling_team_id' => $data['team2_id'],
        ]);

        return response()->json([
            'match_id' => $match->id,
            'inning_id' => $inning->id,
        ], 201);
    }

    // Marks the match finished with the final result.
    public function complete(Request $request, CricketMatch $match)
    {
        $data = $request->validate([
            'winner_team_id' => 'nullable|exists:teams,id',
            'result_text' => 'required|string',
        ]);

        $match->update([
            'status' => 'completed',
            'winner_team_id' => $data['winner_team_id'] ?? null,
            'result_text' => $data['result_text'],
        ]);

        return response()->json(['ok' => true]);
    }

    // Full scorecard: match + both innings + every delivery + per-player aggregates.
    public function scorecard(CricketMatch $match)
    {
        $match->load([
            'team1', 'team2', 'winner', 'tournament',
            'innings.battingTeam', 'innings.bowlingTeam',
            'innings.deliveries.batsman', 'innings.deliveries.bowler', 'innings.deliveries.playerOut',
        ]);

        $match->innings->each(function ($inning) {
            $batting = [];
            $bowling = [];

            foreach ($inning->deliveries as $d) {
                $b = &$batting[$d->batsman_id];
                $b ??= ['player_id' => $d->batsman_id, 'name' => $d->batsman->name, 'runs' => 0, 'balls' => 0, 'fours' => 0, 'sixes' => 0];
                if ($d->is_legal || in_array($d->extra_type, ['B', 'LB'])) {
                    $b['balls'] += 1;
                }
                if (! $d->extra_type) {
                    $b['runs'] += $d->runs;
                    if ($d->runs === 4) {
                        $b['fours'] += 1;
                    }
                    if ($d->runs === 6) {
                        $b['sixes'] += 1;
                    }
                }

                $bw = &$bowling[$d->bowler_id];
                $bw ??= ['player_id' => $d->bowler_id, 'name' => $d->bowler->name, 'runs' => 0, 'balls' => 0, 'wickets' => 0];
                if ($d->is_legal) {
                    $bw['balls'] += 1;
                }
                $bw['runs'] += $d->runs + ($d->extra_type === 'WD' || $d->extra_type === 'NB' ? $d->extra_runs : 0);
                if ($d->is_wicket && $d->wicket_type !== 'RO') {
                    $bw['wickets'] += 1;
                }
            }

            $inning->batting_scorecard = array_values($batting);
            $inning->bowling_scorecard = array_values($bowling);
        });

        return response()->json($match);
    }

    public function index()
    {
        return CricketMatch::with('team1', 'team2', 'tournament')
            ->orderByDesc('created_at')
            ->get();
    }
}
