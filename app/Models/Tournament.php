<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date'];

    public function matches()
    {
        return $this->hasMany(CricketMatch::class, 'tournament_id');
    }
}
