<?php

namespace App\Filament\Resources;
use App\Models\Team;
use App\Models\League;
use App\Filament\Resources\PlayerResource\Pages;
use App\Filament\Resources\PlayerResource\RelationManagers;
use App\Models\Player;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Player Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('birthday')
                    ->required(),
                Forms\Components\TextInput::make('height')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('nationality')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('league_id')
                    ->label('League')
                    ->relationship('league', 'name')
                    ->reactive() // important for dependent selects
                    ->required()
                    ->afterStateUpdated(fn (callable $set) => $set('team_id', null)), // reset team when league changes
                    Forms\Components\Select::make('team_id')
                    ->label('Team')
                    ->required()
                    ->options(function (callable $get) {
                        $leagueId = $get('league_id');
                        if (!$leagueId) {
                            return [];
                        }
                        return Team::where('league_id', $leagueId)->pluck('name', 'id');
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('birthday')->date(),
                Tables\Columns\TextColumn::make('height'),
                Tables\Columns\TextColumn::make('nationality'),
                Tables\Columns\TextColumn::make('league.name')->label('League'),
                Tables\Columns\TextColumn::make('team.name')->label('Team'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayers::route('/'),
            'create' => Pages\CreatePlayer::route('/create'),
            'edit' => Pages\EditPlayer::route('/{record}/edit'),
        ];
    }
}
