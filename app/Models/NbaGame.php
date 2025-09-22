<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NbaGame extends Model
{
    protected $table = 'nba_games';

    protected $fillable = [
        'external_id',
        'schedule_date',
        'tipoff',
        'status',
        'venue',
        'city',
        'home_team_id',
        'home_team_name',
        'home_team_short',
        'home_team_logo',
        'away_team_id',
        'away_team_name',
        'away_team_short',
        'away_team_logo',
    ];
}
