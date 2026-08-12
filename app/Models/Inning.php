<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inning extends Model
{
    protected $fillable = [
        'match_id', 'innings_number', 'batting_team_id', 'bowling_team_id',
        'target', 'total_runs', 'total_wickets', 'total_balls', 'extras', 'status',
    ];

    public function match()
    {
        return $this->belongsTo(CricketMatch::class, 'match_id');
    }

    public function battingTeam()
    {
        return $this->belongsTo(Team::class, 'batting_team_id');
    }

    public function bowlingTeam()
    {
        return $this->belongsTo(Team::class, 'bowling_team_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
