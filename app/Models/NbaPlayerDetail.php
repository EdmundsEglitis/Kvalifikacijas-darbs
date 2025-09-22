<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NbaPlayerDetail extends Model
{
    use HasFactory;

    protected $table = 'nba_player_details';

    protected $fillable = [
        'external_id',
        'uid',
        'guid',
        'first_name',
        'last_name',
        'full_name',
        'display_name',
        'jersey',
        'links',
        'college',
        'college_team',
        'college_athlete',
        'headshot_href',
        'headshot_alt',
        'position',
        'team',
        'active',
        'status',
        'birth_place',
        'display_height',
        'display_weight',
        'display_dob',
        'age',
        'display_jersey',
        'display_experience',
        'display_draft',
    ];

    protected $casts = [
        'links'           => 'array',
        'college'         => 'array',
        'college_team'    => 'array',
        'college_athlete' => 'array',
        'position'        => 'array',
        'team'            => 'array',
        'status'          => 'array',
        'active'          => 'boolean',
    ];
}
