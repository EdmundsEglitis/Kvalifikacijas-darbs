<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerGameStatResource\Pages;
use App\Models\PlayerGameStat;
use App\Models\Game;
use App\Models\Player;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlayerGameStatResource extends Resource
{
    protected static ?string $model = PlayerGameStat::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static function recalc(Set $set, Get $get): void
    {
        $fgm2 = (int) $get('fgm2');
        $fga2 = (int) $get('fga2');
        $fgm3 = (int) $get('fgm3');
        $fga3 = (int) $get('fga3');
        $ftm  = (int) $get('ftm');
        $fta  = (int) $get('fta');
        $oreb = (int) $get('oreb');
        $dreb = (int) $get('dreb');
        $ast  = (int) $get('ast');
        $stl  = (int) $get('stl');
        $blk  = (int) $get('blk');
        $tov  = (int) $get('tov');

        $points = ($fgm2 * 2) + ($fgm3 * 3) + $ftm;
        $reb = $oreb + $dreb;

        $missedFg = ($fga2 + $fga3) - ($fgm2 + $fgm3);
        $missedFt = $fta - $ftm;

        $eff = ($points + $reb + $ast + $stl + $blk) - ($missedFg + $missedFt + $tov);

        $set('points', $points);
        $set('reb', $reb);
        $set('eff', $eff);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('game_id')
                    ->label('Game')
                    ->options(
                        Game::where('date', '<', now())
                            ->with(['team1', 'team2'])
                            ->get()
                            ->mapWithKeys(fn ($game) => [
                                $game->id => $game->team1->name . ' vs ' . $game->team2->name . ' (' . $game->date->format('Y-m-d') . ')',
                            ])
                    )
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (Set $set) => $set('team_id', null)),

                Forms\Components\Select::make('team_id')
                    ->label('Team')
                    ->options(function (Get $get) {
                        $gameId = $get('game_id');
                        if (!$gameId) return [];

                        $game = Game::with(['team1', 'team2'])->find($gameId);
                        if (!$game) return [];

                        return [
                            $game->team1->id => $game->team1->name,
                            $game->team2->id => $game->team2->name,
                        ];
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (Set $set) => $set('player_id', null)),

                    Forms\Components\Select::make('player_id')
                    ->label('Player')
                    ->options(function (Get $get) {
                        $teamId = $get('team_id');
                        $gameId = $get('game_id');
                
                        // Debug dump — will stop execution here
                
                        if (! $teamId || ! $gameId) {
                            return [];
                        }

                        return Player::query()
                            ->where('team_id', $teamId)
                            ->whereDoesntHave('playerGameStats', function ($q) use ($gameId) {
                                $q->where('game_id', $gameId);
                            })
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->reactive(),
                
                
                
                
                
                
                
                
                Forms\Components\TextInput::make('minutes')->numeric()->label('Minutes')->nullable()->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('fgm2')->numeric()->label('2PT Made')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('fga2')->numeric()->label('2PT Attempted')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('fgm3')->numeric()->label('3PT Made')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('fga3')->numeric()->label('3PT Attempted')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('ftm')->numeric()->label('FT Made')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('fta')->numeric()->label('FT Attempted')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('oreb')->numeric()->label('Off. Rebounds')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('dreb')->numeric()->label('Def. Rebounds')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('ast')->numeric()->label('Assists')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('tov')->numeric()->label('Turnovers')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('stl')->numeric()->label('Steals')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('blk')->numeric()->label('Blocks')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('pf')->numeric()->label('Fouls')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('points')->numeric()->label('Points')->default(0)->rules(['integer', 'min:0']),
                Forms\Components\TextInput::make('reb')->numeric()->label('Total Rebounds')->default(0)->rules(['integer', 'min:0']),

                
                // These two are allowed to be negative
                Forms\Components\TextInput::make('plus_minus')->numeric()->label('Plus/Minus')->default(0),
                Forms\Components\TextInput::make('eff')->numeric()->label('Efficiency (EFF)')->disabled()->dehydrated(true),
                

                // === Manual calculation button ===
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('recalc')
                        ->label('Recalculate Stats')
                        ->color('primary')
                        ->action(function (Set $set, Get $get) {
                            self::recalc($set, $get);
                            Notification::make()
                                ->title('Stats recalculated successfully!')
                                ->success()
                                ->send();
                        }),
                ]),

                // Status
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'played' => 'Played',
                        'dnp' => 'Did Not Play',
                    ])
                    ->default('played')
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if ($state === 'dnp') {
                            $fields = ['fgm2','fga2','fgm3','fga3','ftm','fta','oreb','dreb','ast','tov','stl','blk','pf','plus_minus','points','reb','eff'];
                            foreach ($fields as $field) $set($field, 0);
                            $set('minutes', null);
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('game.id')->label('Game ID'),
                Tables\Columns\TextColumn::make('team.name')->label('Team'),
                Tables\Columns\TextColumn::make('player.name')->label('Player'),
                Tables\Columns\TextColumn::make('points')->label('PTS')->sortable(),
                Tables\Columns\TextColumn::make('reb')->label('REB'),
                Tables\Columns\TextColumn::make('ast')->label('AST'),
                Tables\Columns\TextColumn::make('eff')->label('EFF'),
                Tables\Columns\TextColumn::make('status')->label('Status'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayerGameStats::route('/'),
            'create' => Pages\CreatePlayerGameStat::route('/create'),
            'edit' => Pages\EditPlayerGameStat::route('/{record}/edit'),
        ];
    }
}
