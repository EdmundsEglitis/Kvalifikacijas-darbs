<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Models\Game;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    /** Calculation logic, reusable */
    protected static function recalc(Set $set, Get $get): void
    {
        $q1_1 = (int) $get('team1_q1');
        $q2_1 = (int) $get('team1_q2');
        $q3_1 = (int) $get('team1_q3');
        $q4_1 = (int) $get('team1_q4');

        $q1_2 = (int) $get('team2_q1');
        $q2_2 = (int) $get('team2_q2');
        $q3_2 = (int) $get('team2_q3');
        $q4_2 = (int) $get('team2_q4');

        $team1Total = $q1_1 + $q2_1 + $q3_1 + $q4_1;
        $team2Total = $q1_2 + $q2_2 + $q3_2 + $q4_2;

        $set('score', "{$team1Total}-{$team2Total}");

        if ($team1Total > $team2Total) {
            $set('winner_id', $get('team1_id'));
        } elseif ($team2Total > $team1Total) {
            $set('winner_id', $get('team2_id'));
        } else {
            $set('winner_id', null); // tie
        }
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DateTimePicker::make('date')
                ->label('Game Date & Time')
                ->required(),

            Forms\Components\Select::make('team1_id')
                ->label('Team 1')
                ->options(Team::pluck('name', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn(Set $set) => $set('team2_id', null)),

            Forms\Components\Select::make('team2_id')
                ->label('Team 2')
                ->options(fn(Get $get) => Team::where('id', '!=', $get('team1_id'))->pluck('name', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('team1_q1')->numeric()->label('Team 1 - Q1')->default(0),
            Forms\Components\TextInput::make('team2_q1')->numeric()->label('Team 2 - Q1')->default(0),

            Forms\Components\TextInput::make('team1_q2')->numeric()->label('Team 1 - Q2')->default(0),
            Forms\Components\TextInput::make('team2_q2')->numeric()->label('Team 2 - Q2')->default(0),

            Forms\Components\TextInput::make('team1_q3')->numeric()->label('Team 1 - Q3')->default(0),
            Forms\Components\TextInput::make('team2_q3')->numeric()->label('Team 2 - Q3')->default(0),

            Forms\Components\TextInput::make('team1_q4')->numeric()->label('Team 1 - Q4')->default(0),
            Forms\Components\TextInput::make('team2_q4')->numeric()->label('Team 2 - Q4')->default(0),

            Forms\Components\TextInput::make('score')
                ->label('Final Score')
                ->disabled()
                ->dehydrated(true),

            Forms\Components\Select::make('winner_id')
                ->label('Winner')
                ->options(Team::pluck('name', 'id'))
                ->nullable()
                ->disabled()
                ->dehydrated(true),

            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('calculateTotals')
                    ->label('Calculate Totals')
                    ->action(function (Set $set, Get $get) {
                        self::recalc($set, $get);

                        Notification::make()
                            ->title('Totals Calculated')
                            ->success()
                            ->send();
                    })
                    ->color('primary'),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('date')->dateTime()->sortable()->label('Game Date'),
            Tables\Columns\TextColumn::make('team1.name')->label('Team 1'),
            Tables\Columns\TextColumn::make('team2.name')->label('Team 2'),
            Tables\Columns\TextColumn::make('score')->label('Final Score')->default('—'),
            Tables\Columns\TextColumn::make('winner.name')->label('Winner')->default(fn($record) => $record->date < now() ? 'TBD' : 'Upcoming'),
        ])
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
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
