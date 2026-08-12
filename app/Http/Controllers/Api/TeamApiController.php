<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamApiController extends Controller
{
    public function index()
    {
        return Team::orderBy('name')->get(['id', 'name', 'short_name']);
    }
}
