<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;

class TournamentApiController extends Controller
{
    public function index()
    {
        return Tournament::orderBy('name')->get(['id', 'name']);
    }
}
