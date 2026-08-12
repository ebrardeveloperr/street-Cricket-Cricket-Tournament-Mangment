<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CricketMatch extends Model
{
    // Table is "matches" - class is named CricketMatch because
    // "Match" collides with PHP's reserved match() expression.
    protected $table = 'matches';

    protected $fillable = [
        'tournament_id', 'team1_id', 'team2_id', 'total_overs',
        'status', 'winner_team_id', 'result_text', 'match_date',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function winner()
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function innings()
    {
        return $this->hasMany(Inning::class, 'match_id');
    }
}
