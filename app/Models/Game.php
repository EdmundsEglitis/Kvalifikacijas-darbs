<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'team1_id',
        'team2_id',
        'score',
        'team11st',
        'team21st',
        'team12st',
        'team22st',
        'team13st',
        'team23st',
        'team14st',
        'team24st',
        'winner_id',
    ];

    protected $casts = [
        'date' => 'datetime', 
    ];

    protected $dates = ['date'];

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
        return $this->belongsTo(Team::class, 'winner_id');
    }
      public function scopeCompleted($query)
    {
        return $query->where('date', '<', Carbon::now());
    }

    public function playerStats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }
    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_game_stats')
                    ->withPivot([
                        'team_id',
                        'minutes', 'points',
                        'fgm2', 'fga2',
                        'fgm3', 'fga3',
                        'ftm', 'fta',
                        'oreb', 'dreb', 'reb',
                        'ast', 'tov', 'stl', 'blk', 'pf',
                        'eff', 'plus_minus',
                        'status',
                    ])
                    ->withTimestamps();
    }
    
    public function playerGameStats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }


}