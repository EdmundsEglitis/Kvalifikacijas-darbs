<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NbaTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
        'short_name',
        'abbreviation',
        'logo',
        'logo_dark',
        'url',
    ];
    public function getRouteKeyName()
    {
        return 'external_id';
    }

    public function players()
    {
        return $this->hasMany(NbaPlayer::class, 'team_id', 'external_id');
    }
}
