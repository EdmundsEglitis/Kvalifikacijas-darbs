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

    // Player belongs to a League
    public function league()
    {
        return $this->belongsTo(League::class);
    }

    // Player belongs to a Team
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function stats()
{
    return $this->hasMany(PlayerGameStat::class);
}
// Player.php
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
