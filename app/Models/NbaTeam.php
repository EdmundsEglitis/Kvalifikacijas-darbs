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
}
