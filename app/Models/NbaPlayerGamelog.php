<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NbaPlayerGameLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'event_id',

        // new metadata fields
        'game_date',
        'opponent_name',
        'opponent_logo',
        'result',
        'score',

        // stats
        'minutes',
        'fg',
        'fg_pct',
        'three_pt',
        'three_pt_pct',
        'ft',
        'ft_pct',
        'rebounds',
        'assists',
        'steals',
        'blocks',
        'turnovers',
        'fouls',
        'points',
    ];

    /**
     * Relationship: this log belongs to a player
     */
    public function player()
    {
        return $this->belongsTo(NbaPlayer::class, 'player_id');
    }
}
