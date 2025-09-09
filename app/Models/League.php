<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id']; // allow mass assignment
public function teams()
{
    return $this->hasMany(Team::class);
}

public function players()
{
    return $this->hasMany(Player::class);
}
public function parent()
{
    return $this->belongsTo(League::class, 'parent_id');
}

public function children()
{
    return $this->hasMany(League::class, 'parent_id');
}
}
