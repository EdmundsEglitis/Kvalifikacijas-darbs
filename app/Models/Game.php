<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'team1_id',
        'team2_id',
        'score',

        'team11st','team21st',
        'team12st','team22st',
        'team13st','team23st',
        'team14st','team24st',
        'winner_id',
    ];

public function getTeam11stAttribute() { return $this->attributes['team1_q1'] ?? null; }
public function setTeam11stAttribute($value) { $this->attributes['team1_q1'] = (int) $value; }


public function getTeam21stAttribute() { return $this->attributes['team2_q1'] ?? null; }
public function setTeam21stAttribute($value) { $this->attributes['team2_q1'] = (int) $value; }


public function getTeam12stAttribute() { return $this->attributes['team1_q2'] ?? null; }
public function setTeam12stAttribute($value) { $this->attributes['team1_q2'] = (int) $value; }


public function getTeam22stAttribute() { return $this->attributes['team2_q2'] ?? null; }
public function setTeam22stAttribute($value) { $this->attributes['team2_q2'] = (int) $value; }


public function getTeam13stAttribute() { return $this->attributes['team1_q3'] ?? null; }
public function setTeam13stAttribute($value) { $this->attributes['team1_q3'] = (int) $value; }


public function getTeam23stAttribute() { return $this->attributes['team2_q3'] ?? null; }
public function setTeam23stAttribute($value) { $this->attributes['team2_q3'] = (int) $value; }


public function getTeam14stAttribute() { return $this->attributes['team1_q4'] ?? null; }
public function setTeam14stAttribute($value) { $this->attributes['team1_q4'] = (int) $value; }


public function getTeam24stAttribute() { return $this->attributes['team2_q4'] ?? null; }
public function setTeam24stAttribute($value) { $this->attributes['team2_q4'] = (int) $value; }

    protected $casts = [
        'date'      => 'datetime',
        'team1_q1'  => 'integer',
        'team1_q2'  => 'integer',
        'team1_q3'  => 'integer',
        'team1_q4'  => 'integer',
        'team2_q1'  => 'integer',
        'team2_q2'  => 'integer',
        'team2_q3'  => 'integer',
        'team2_q4'  => 'integer',
    ];

    protected $dates = ['date'];


    protected $appends = [
        'team1_total', 'team2_total', 'final_score', 'is_completed',
    ];


    public function team1() { return $this->belongsTo(Team::class, 'team1_id'); }
    public function team2() { return $this->belongsTo(Team::class, 'team2_id'); }
    public function winner(){ return $this->belongsTo(Team::class, 'winner_id'); }

    public function scopeCompleted($query)
    {
        return $query->where('date', '<', Carbon::now());
    }

    public function playerStats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_game_stats')
            ->withPivot([
                'team_id','minutes','points',
                'fgm2','fga2','fgm3','fga3',
                'ftm','fta','oreb','dreb','reb',
                'ast','tov','stl','blk','pf',
                'eff','plus_minus','status',
            ])
            ->withTimestamps();
    }

    public function playerGameStats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }


     public function getTeam1TotalAttribute(): ?int
     {
         $parts = [$this->team11st, $this->team12st, $this->team13st, $this->team14st];
         $hasAny = collect($parts)->contains(fn($v) => $v !== null);
         return $hasAny ? collect($parts)->sum(fn($v) => (int) $v) : null;
     }
     
     public function getTeam2TotalAttribute(): ?int
     {
         $parts = [$this->team21st, $this->team22st, $this->team23st, $this->team24st];
         $hasAny = collect($parts)->contains(fn($v) => $v !== null);
         return $hasAny ? collect($parts)->sum(fn($v) => (int) $v) : null;
     }


    public function getFinalScoreAttribute(): ?string
    {
        if (!empty($this->score)) {
            return $this->score;
        }
        if (!is_null($this->team1_total) && !is_null($this->team2_total)) {
            return "{$this->team1_total}-{$this->team2_total}";
        }
        return null;
    }


    public function getIsCompletedAttribute(): bool
    {
        return !is_null($this->winner_id) || !is_null($this->final_score);
    }
    
}
