<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    protected $fillable = ['id', 'date', 'warning', 'player_id'];
    public $timestamps = false;

    // Dummy table name so Eloquent is happy
    public function getTable()
    {
        return 'warnings';
    }
}
