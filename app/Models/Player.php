<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'birthday',
        'height',
        'nationality',
        'photo',
        'jersey_number',
        'league_id',
        'team_id',
    ];

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function stats()
{
    return $this->hasMany(PlayerGameStat::class);
}
public function games()
{
    return $this->belongsToMany(Game::class, 'player_game_stats')
                ->withPivot([
                    'points', 'reb', 'ast', 'stl', 'blk',
                    'fgm2', 'fga2', 'fgm3', 'fga3',
                    'ftm', 'fta', 'oreb', 'dreb', 'tov', 'pf', 'eff'
                ]);
}
public function playerGameStats()
{
    return $this->hasMany(PlayerGameStat::class, 'player_id');
}



}
