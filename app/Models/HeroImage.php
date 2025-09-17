<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroImage extends Model
{
    protected $fillable = ['title', 'image_path', 'location', 'league_id'];

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    // Determine type: home | league | subleague
    public function getLocationAttribute(): string
    {
        if (is_null($this->league_id)) {
            return 'home';
        }

        return $this->league->parent_id ? 'subleague' : 'league';
    }

    // Friendly label with icon
    public function getDisplayOnAttribute(): string
    {
        if (is_null($this->league_id)) {
            return '🏠 Homepage';
        }

        $name = $this->league->name;
        return $this->league->parent_id
            ? "↳ Sub-League: {$name}"
            : "🏀 League: {$name}";
    }
}


