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

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('league_id')
                    ->label('League (Sub-league only)')
                    // Only show child leagues
                    ->options(fn () => League::query()
                        ->whereNotNull('parent_id')
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Teams can only be assigned to sub-leagues.')
                    // Dynamic validation: ensure the chosen league is a child
                    ->rule(fn (Get $get) => function (string $attribute, $value, Closure $fail) {
                        if (blank($value)) {
                            return;
                        }
                        $league = League::find($value);
                        if (! $league) {
                            $fail('Selected league does not exist.');
                            return;
                        }
                        if ($league->parent_id === null) {
                            $fail('Teams cannot be assigned to a main league. Please select a sub-league.');
                        }
                    }),
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

                // Show both parent (main) and the sub-league names for clarity
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
