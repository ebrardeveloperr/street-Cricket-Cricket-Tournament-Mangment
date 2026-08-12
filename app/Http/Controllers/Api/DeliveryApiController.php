<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Inning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryApiController extends Controller
{
    // Records one ball and updates the innings running totals.
    public function store(Request $request)
    {
        $data = $request->validate([
            'inning_id' => 'required|exists:innings,id',
            'over_number' => 'required|integer',
            'ball_number' => 'required|integer',
            'batsman_id' => 'required|exists:players,id',
            'non_striker_id' => 'required|exists:players,id',
            'bowler_id' => 'required|exists:players,id',
            'runs' => 'integer',
            'extra_type' => 'nullable|string|in:WD,NB,B,LB',
            'extra_runs' => 'integer',
            'is_wicket' => 'boolean',
            'wicket_type' => 'nullable|string',
            'player_out_id' => 'nullable|exists:players,id',
            'is_legal' => 'boolean',
        ]);

        $delivery = DB::transaction(function () use ($data) {
            $delivery = Delivery::create($data);
            $this->recomputeInningTotals($data['inning_id']);

            return $delivery;
        });

        return response()->json(['delivery_id' => $delivery->id], 201);
    }

    // Undo: removes the most recently recorded ball for an innings.
    public function destroyLatest(Inning $inning)
    {
        DB::transaction(function () use ($inning) {
            $last = $inning->deliveries()->orderByDesc('id')->first();
            if ($last) {
                $last->delete();
            }
            $this->recomputeInningTotals($inning->id);
        });

        return response()->json(['ok' => true]);
    }

    // Recalculates total_runs / total_wickets / total_balls / extras
    // straight from the deliveries table, so totals can never drift out of sync.
    private function recomputeInningTotals(int $inningId): void
    {
        $deliveries = Delivery::where('inning_id', $inningId)->get();

        $runs = 0;
        $extras = 0;
        $wickets = 0;
        $balls = 0;

        foreach ($deliveries as $d) {
            $runs += $d->runs + $d->extra_runs;
            if ($d->extra_type) {
                $extras += $d->extra_runs;
            }
            if ($d->is_wicket) {
                $wickets += 1;
            }
            if ($d->is_legal) {
                $balls += 1;
            }
        }

        Inning::where('id', $inningId)->update([
            'total_runs' => $runs,
            'total_wickets' => $wickets,
            'total_balls' => $balls,
            'extras' => $extras,
        ]);
    }
}
