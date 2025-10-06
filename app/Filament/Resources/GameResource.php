<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Models\Game;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    /** pure helper: totals + winner (NO Get/Set here, just plain ints) */
    private static function computeTotals(
        int $t11, int $t12, int $t13, int $t14,
        int $t21, int $t22, int $t23, int $t24,
        ?int $team1Id, ?int $team2Id
    ): array {
        $t1 = $t11 + $t12 + $t13 + $t14;
        $t2 = $t21 + $t22 + $t23 + $t24;

        $winnerId = null;
        if ($team1Id && $team2Id) {
            if ($t1 > $t2) $winnerId = $team1Id;
            elseif ($t2 > $t1) $winnerId = $team2Id;
        }
        return [$t1, $t2, $winnerId];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DateTimePicker::make('date')
                ->label('Game Date & Time')
                ->required(),

            Forms\Components\Select::make('team1_id')
                ->label('Team 1')
                ->options(fn () => Team::query()->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn (Set $set) => $set('team2_id', null)),

            Forms\Components\Select::make('team2_id')
                ->label('Team 2')
                ->options(fn (Get $get) => Team::query()
                    ->when($get('team1_id'), fn ($q) => $q->where('id', '!=', $get('team1_id')))
                    ->pluck('name', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\Fieldset::make('Quarters')
                ->schema([
                    // YOUR model fields:
                    Forms\Components\TextInput::make('team11st')->label('Team 1 – Q1')
                        ->default(0)->numeric()->rules(['required','integer','min:0']),
                    Forms\Components\TextInput::make('team21st')->label('Team 2 – Q1')
                        ->default(0)->numeric()->rules(['required','integer','min:0']),

                    Forms\Components\TextInput::make('team12st')->label('Team 1 – Q2')
                        ->default(0)->numeric()->rules(['required','integer','min:0']),
                    Forms\Components\TextInput::make('team22st')->label('Team 2 – Q2')
                        ->default(0)->numeric()->rules(['required','integer','min:0']),

                    Forms\Components\TextInput::make('team13st')->label('Team 1 – Q3')
                        ->default(0)->numeric()->rules(['required','integer','min:0']),
                    Forms\Components\TextInput::make('team23st')->label('Team 2 – Q3')
                        ->default(0)->numeric()->rules(['required','integer','min:0']),

                    Forms\Components\TextInput::make('team14st')->label('Team 1 – Q4')
                        ->default(0)->numeric()->rules(['required','integer','min:0']),
                    Forms\Components\TextInput::make('team24st')->label('Team 2 – Q4')
                        ->default(0)->numeric()->rules(['required','integer','min:0']),
                ])
                ->columns(4),

            Forms\Components\TextInput::make('score')
                ->label('Final Score')
                ->disabled()
                ->dehydrated(true),

            Forms\Components\Select::make('winner_id')
                ->label('Winner')
                ->options(fn () => Team::query()->pluck('name', 'id'))
                ->nullable()
                ->disabled()
                ->dehydrated(true),

            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('calculateTotals')
                    ->label('Calculate Totals')
                    ->color('primary')
                    ->action(function (Set $set, Get $get) {
                        // Read raw values from the form, coerce to ints
                        $t11 = (int) $get('team11st');
                        $t12 = (int) $get('team12st');
                        $t13 = (int) $get('team13st');
                        $t14 = (int) $get('team14st');

                        $t21 = (int) $get('team21st');
                        $t22 = (int) $get('team22st');
                        $t23 = (int) $get('team23st');
                        $t24 = (int) $get('team24st');

                        $team1Id = $get('team1_id');
                        $team2Id = $get('team2_id');

                        [$t1, $t2, $winnerId] = self::computeTotals(
                            $t11,$t12,$t13,$t14,
                            $t21,$t22,$t23,$t24,
                            $team1Id, $team2Id
                        );

                        // Write results back to the form
                        $set('score', "{$t1}-{$t2}");
                        $set('winner_id', $winnerId);
                    }),
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
            Tables\Columns\TextColumn::make('winner.name')->label('Winner')
                ->default(fn ($record) => $record->date && $record->date->isPast() ? 'TBD' : 'Upcoming'),
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
            'index'  => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit'   => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
