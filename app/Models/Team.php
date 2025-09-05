<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'league_id'];

    // Define relationship to League
    public function league()
    {
        return $this->belongsTo(League::class);
    }
    public function stats()
{
    return $this->hasMany(PlayerGameStat::class);
}

    
}
