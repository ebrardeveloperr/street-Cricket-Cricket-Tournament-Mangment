<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'inning_id', 'over_number', 'ball_number',
        'batsman_id', 'non_striker_id', 'bowler_id',
        'runs', 'extra_type', 'extra_runs',
        'is_wicket', 'wicket_type', 'player_out_id', 'is_legal',
    ];

    protected $casts = [
        'is_wicket' => 'boolean',
        'is_legal' => 'boolean',
    ];

    public function inning()
    {
        return $this->belongsTo(Inning::class);
    }

    public function batsman()
    {
        return $this->belongsTo(Player::class, 'batsman_id');
    }

    public function nonStriker()
    {
        return $this->belongsTo(Player::class, 'non_striker_id');
    }

    public function bowler()
    {
        return $this->belongsTo(Player::class, 'bowler_id');
    }

    public function playerOut()
    {
        return $this->belongsTo(Player::class, 'player_out_id');
    }
}
