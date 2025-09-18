<?php

namespace App\Filament\Widgets;

use App\Models\Warning;
use App\Models\Game;
use App\Models\PlayerGameStat;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class SystemWarnings extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(Game::query()) // dummy query (required by TableWidget)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Game ID')
                    ->sortable()
                    ->searchable(),
    
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
    
                Tables\Columns\TextColumn::make('warning')
                    ->label('Issue')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->color(fn ($state) => str_contains($state, 'No score') ? 'danger' : 'warning')
                    ->url(function ($record) {
                        // Detect Filament v2 vs v3 route names
                        $gameEditRoute = app()->routes->hasNamedRoute('filament.resources.games.edit')
                            ? 'filament.resources.games.edit'
                            : 'filament.admin.resources.games.edit';
    
                        $statCreateRoute = app()->routes->hasNamedRoute('filament.resources.player-game-stats.create')
                            ? 'filament.resources.player-game-stats.create'
                            : 'filament.admin.resources.player-game-stats.create';
    
                        // Link to Game edit for "No score" warnings
                        if (str_contains($record->warning, 'No score')) {
                            return route($gameEditRoute, $record->id);
                        }
    
                        // Link to PlayerGameStat create for "Missing stats" warnings
                        if (str_contains($record->warning, 'Missing stats') && $record->player_id) {
                            return route($statCreateRoute) .
                                '?game_id=' . $record->id .
                                '&player_id=' . $record->player_id;
                        }
    
                        return null;
                    })
                    ->openUrlInNewTab(),
            ])
            ->filters([])
            ->searchDebounce(500);
    }
    

    public function getTableRecords(): EloquentCollection
    {
        $warnings = new EloquentCollection();
    
        // Build synthetic warnings
        $games = Game::with(['team1.players', 'team2.players'])->get();
    
        foreach ($games as $game) {
            // Past game with no score
            if ($game->date->lt(now()) && (empty($game->score) || $game->score === '')) {
                $warnings->push(new Warning([
                    'id'      => $game->id,
                    'date'    => $game->date,
                    'warning' => 'No score entered for game on ' . $game->date->format('Y-m-d H:i'),
                ]));
                continue;
            }
    
            // Scored game but missing stats
            if (!empty($game->score)) {
                $teamPlayers = $game->team1->players->merge($game->team2->players);
    
                foreach ($teamPlayers as $player) {
                    $hasStat = PlayerGameStat::where('game_id', $game->id)
                        ->where('player_id', $player->id)
                        ->exists();
    
                    if (!$hasStat) {
                        $warnings->push(new Warning([
                            'id'        => $game->id,
                            'date'      => $game->date,
                            'warning'   => 'Missing stats for player ' . $player->name . ' in game on ' . $game->date->format('Y-m-d H:i'),
                            'player_id' => $player->id,
                        ]));
                    }
                }
            }
        }
    
        // Apply global search from the table (use widget helper)
        $search = $this->getTableSearch(); // string|null
        if ($search !== null && $search !== '') {
            $needle = mb_strtolower($search);
            $warnings = $warnings->filter(function ($record) use ($needle) {
                if (mb_stripos((string) $record->id, $needle) !== false) {
                    return true;
                }
                if (mb_stripos($record->warning, $needle) !== false) {
                    return true;
                }
                if (mb_stripos($record->date->format('Y-m-d H:i'), $needle) !== false) {
                    return true;
                }
                return false;
            })->values();
        }
    
        // Apply sorting from column header clicks (use widget helpers)
        $sortColumn = $this->getTableSortColumn();      // 'id' | 'date' | 'warning' | null
        $sortDir    = $this->getTableSortDirection();   // 'asc' | 'desc' | null
    
        if ($sortColumn) {
            $warnings = $warnings->sortBy(
                function ($record) use ($sortColumn) {
                    $value = $record->{$sortColumn} ?? null;
    
                    // Normalize types for consistent sorting
                    if ($sortColumn === 'date' && $value) {
                        return $value instanceof \Carbon\Carbon ? $value->timestamp : (string) $value;
                    }
    
                    // Lowercase strings to avoid case jitter
                    return is_string($value) ? mb_strtolower($value) : $value;
                },
                SORT_REGULAR,
                $sortDir === 'desc'
            )->values();
        }
    
        return new EloquentCollection($warnings);
    }
}