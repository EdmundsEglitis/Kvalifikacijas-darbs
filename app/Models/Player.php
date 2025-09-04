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
}
