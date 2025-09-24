<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class NbaPlayer extends Model
{
    protected $fillable = [
        'external_id',
        'guid',
        'uid',
        'first_name',
        'last_name',
        'full_name',
        'display_weight',
        'display_height',
        'age',
        'salary',
        'image',
        'team_id',
        'team_name',
        'team_logo',
    ];

// In App\Models\NbaPlayer.php
public function gamelogs()
{
    return $this->hasMany(NbaPlayerGameLog::class, 'player_external_id', 'external_id');
}


}

