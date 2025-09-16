<?php

namespace App\Filament\Resources;

use App\Models\Team;
use App\Models\League;
use App\Models\Player;
use App\Filament\Resources\PlayerResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->label('Birthday')
                    ->nullable(),


                    Forms\Components\TextInput::make('height')
                        ->numeric()
                        ->label('Height (cm)')
                        ->nullable()
                        ->rules(['nullable', 'numeric', 'min:0']),


                Forms\Components\TextInput::make('nationality')
                    ->nullable()
                    ->maxLength(255),

                    Forms\Components\FileUpload::make('photo')
                    ->label('Upload Photo')
                    ->image()
                    ->directory('players')
                    ->disk('public')             // 👈 ensures Filament saves in public disk
                    ->visibility('public')       // 👈 makes it accessible
                    ->nullable()
                    ->imagePreviewHeight('150')  // 👈 shows preview in edit form
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull(),
                

                // Step 1: Parent League
                Forms\Components\Select::make('parent_league_id')
                    ->label('Parent League')
                    ->options(League::whereNull('parent_id')->pluck('name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => [
                        $set('league_id', null),
                        $set('team_id', null),
                    ])
                    ->required(),

                // Step 2: Child League (filtered by parent)
                Forms\Components\Select::make('league_id')
                    ->label('Sub-League')
                    ->options(fn (Get $get) => $get('parent_league_id')
                        ? League::where('parent_id', $get('parent_league_id'))->pluck('name', 'id')
                        : [])
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('team_id', null))
                    ->required(),

                // Step 3: Team (filtered by child league)
                Forms\Components\Select::make('team_id')
                    ->label('Team')
                    ->options(fn (Get $get) => $get('league_id')
                        ? Team::where('league_id', $get('league_id'))->pluck('name', 'id')
                        : [])
                    ->searchable()
                    ->required(),


                Forms\Components\TextInput::make('jersey_number')
                    ->numeric()
                    ->label('Jersey Number')
                    ->nullable()
                    ->rules(['nullable', 'integer', 'min:0']),
                



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('birthday')->date(),
                Tables\Columns\TextColumn::make('height'),
                Tables\Columns\TextColumn::make('nationality'),
                Tables\Columns\ImageColumn::make('photo')
                ->label('Photo')
                ->disk('public')
                ->height(50)
                ->width(50)
                ->rounded(), // circular thumbnail

                Tables\Columns\TextColumn::make('league.parent.name')->label('Parent League'),
                Tables\Columns\TextColumn::make('league.name')->label('Sub-League'),
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
        return [];
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
