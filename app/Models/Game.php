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
        // custom quarter columns you already use:
        'team11st','team21st',
        'team12st','team22st',
        'team13st','team23st',
        'team14st','team24st',
        'winner_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $dates = ['date'];

    // Optional: expose computed attrs on JSON/array
    protected $appends = [
        'team1_total', 'team2_total', 'final_score', 'is_completed',
    ];

    /* ----------------
     |  Relationships  |
     -----------------*/
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

    /* ----------------
     |  Accessors      |
     -----------------*/

    // Sum quarters for Team 1 (null-safe)
    public function getTeam1TotalAttribute(): ?int
    {
        $parts = [$this->team11st, $this->team12st, $this->team13st, $this->team14st];
        $hasAny = collect($parts)->some(fn($v) => $v !== null);
        return $hasAny ? collect($parts)->sum(fn($v) => (int)$v) : null;
    }

    // Sum quarters for Team 2 (null-safe)
    public function getTeam2TotalAttribute(): ?int
    {
        $parts = [$this->team21st, $this->team22st, $this->team23st, $this->team24st];
        $hasAny = collect($parts)->some(fn($v) => $v !== null);
        return $hasAny ? collect($parts)->sum(fn($v) => (int)$v) : null;
    }

    // Final score string preference:
    // 1) use `score` if present (e.g., "89-76")
    // 2) else use summed quarters if both totals available
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

    // Completed if we know a winner or if we can compute a final score
    public function getIsCompletedAttribute(): bool
    {
        return !is_null($this->winner_id) || !is_null($this->final_score);
    }
    
}
