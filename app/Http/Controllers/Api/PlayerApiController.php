<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Player::query()->orderBy('name');

        if ($request->has('team_id')) {
            $query->where('team_id', $request->input('team_id'));
        }

        return $query->get(['id', 'team_id', 'name', 'role']);
    }
}
