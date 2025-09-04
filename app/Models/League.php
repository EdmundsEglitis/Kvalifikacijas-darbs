<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    protected $fillable = ['name']; // allow mass assignment
public function teams()
{
    return $this->hasMany(Team::class);
}

public function players()
{
    return $this->hasMany(Player::class);
}
}
