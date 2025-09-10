<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerGameStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'team_id',
        'minutes',
        'points',
        'fgm2', 'fga2',
        'fgm3', 'fga3',
        'ftm', 'fta',
        'oreb', 'dreb', 'reb',
        'ast', 'tov', 'stl', 'blk', 'pf',
        'eff', 'plus_minus',
        'status',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public static function rules(): array
{
    return [
        'fgm2' => 'lte:fga2',
        'fgm3' => 'lte:fga3',
        'ftm'  => 'lte:fta',
    ];
}






}
