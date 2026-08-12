<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('team')->orderBy('name')->get();
        $teams = Team::orderBy('name')->get();

        return view('manage.players', compact('players', 'teams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'name' => 'required|string|max:255',
            'role' => 'required|in:Batter,Bowler,All-Rounder,Wicket-Keeper',
        ]);

        Player::create($data);

        return back()->with('status', 'Player added.');
    }

    public function destroy(Player $player)
    {
        $player->delete();

        return back()->with('status', 'Player deleted.');
    }
}
