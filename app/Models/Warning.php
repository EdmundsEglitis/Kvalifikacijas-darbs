<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    protected $fillable = ['id', 'date', 'warning', 'player_id'];
    public $timestamps = false;

    public function getTable()
    {
        return 'warnings';
    }
}
