<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NbaStanding extends Model
{
    protected $fillable = [
        'team_id',
        'team_name',
        'abbreviation',
        'wins',
        'losses',
        'win_percent',
        'playoff_seed',
        'games_behind',
        'avg_points_for',
        'avg_points_against',
        'point_differential',
        'points',
        'points_for',
        'points_against',
        'division_win_percent',
        'league_win_percent',
        'streak',
        'clincher',
        'league_standings',
        'home_record',
        'road_record',
        'division_record',
        'conference_record',
        'last_ten',
        'season',
    ];
}
