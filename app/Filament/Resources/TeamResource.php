<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use App\Models\League;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Team Name')
                    ->required()
                    ->maxLength(255),
    
                Forms\Components\Select::make('league_id')
                    ->label('League')
                    ->options(
                        League::whereNotNull('parent_id')->pluck('name', 'id') // ✅ only sub-leagues
                    )
                    ->searchable()
                    ->required(),
    
                Forms\Components\FileUpload::make('logo')
                    ->label('Team Logo')
                    ->image()
                    ->disk('public')
                    ->directory('team-logos')
                    ->imagePreviewHeight('150')
                    ->openable()
                    ->downloadable()
                    ->nullable(),
            ]);
    }
    
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('id')
                ->sortable(),

            Tables\Columns\TextColumn::make('name')
                ->searchable(),

                ImageColumn::make('logo')
                ->label('Team Logo')
                ->disk('public')  // 👈 ensures it uses the correct storage disk
                ->square()
                ->height(50),
            

            Tables\Columns\TextColumn::make('league.parent.name')
                ->label('Main League')
                ->toggleable()
                ->sortable()
                ->default('—'),

            Tables\Columns\TextColumn::make('league.name')
                ->label('Sub-League')
                ->sortable()
                ->searchable(),
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
