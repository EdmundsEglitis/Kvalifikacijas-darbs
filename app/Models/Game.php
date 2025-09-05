<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'date' => 'datetime', // 👈 this makes $game->date a Carbon instance
    ];

    protected $dates = ['date']; // so $game->date->format() works

    // Relationships
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

    // NEW: player stats for this game
    public function playerStats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }
}
