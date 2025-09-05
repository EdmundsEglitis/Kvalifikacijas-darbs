<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Models\Game;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('date')
                    ->label('Game Date & Time')
                    ->required(),

                Forms\Components\Select::make('team1_id')
                    ->label('Team 1')
                    ->options(Team::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('team2_id', null)),

                Forms\Components\Select::make('team2_id')
                    ->label('Team 2')
                    ->options(function (callable $get) {
                        $team1Id = $get('team1_id');
                        return Team::where('id', '!=', $team1Id)->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('score')
                    ->label('Final Score (e.g. 89-76)')
                    ->nullable(),

                Forms\Components\Section::make('Quarters')
                    ->schema([
                        Forms\Components\TextInput::make('team1_q1')->numeric()->label('Team 1 - Q1')->nullable(),
                        Forms\Components\TextInput::make('team2_q1')->numeric()->label('Team 2 - Q1')->nullable(),
                        Forms\Components\TextInput::make('team1_q2')->numeric()->label('Team 1 - Q2')->nullable(),
                        Forms\Components\TextInput::make('team2_q2')->numeric()->label('Team 2 - Q2')->nullable(),
                        Forms\Components\TextInput::make('team1_q3')->numeric()->label('Team 1 - Q3')->nullable(),
                        Forms\Components\TextInput::make('team2_q3')->numeric()->label('Team 2 - Q3')->nullable(),
                        Forms\Components\TextInput::make('team1_q4')->numeric()->label('Team 1 - Q4')->nullable(),
                        Forms\Components\TextInput::make('team2_q4')->numeric()->label('Team 2 - Q4')->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Select::make('winner_id')
                    ->label('Winner')
                    ->options(Team::pluck('name', 'id'))
                    ->nullable()
                    ->helperText('Leave empty until game is finished'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->dateTime()
                    ->sortable()
                    ->label('Game Date'),

                Tables\Columns\TextColumn::make('team1.name')
                    ->label('Team 1'),

                Tables\Columns\TextColumn::make('team2.name')
                    ->label('Team 2'),

                Tables\Columns\TextColumn::make('score')
                    ->label('Final Score')
                    ->default('—'),

                Tables\Columns\TextColumn::make('winner.name')
                    ->label('Winner')
                    ->default(fn ($record) => $record->date < now() ? 'TBD' : 'Upcoming'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->date < now() ? 'Completed' : 'Upcoming'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'upcoming' => 'Upcoming',
                        'completed' => 'Completed',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (($data['value'] ?? null) === 'upcoming') {
                            $query->where('date', '>', now());
                        } elseif (($data['value'] ?? null) === 'completed') {
                            $query->where('date', '<=', now());
                        }
                    }),
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
        return [
            // Later we can add RelationManager for PlayerGameStats under each game
        ];
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
