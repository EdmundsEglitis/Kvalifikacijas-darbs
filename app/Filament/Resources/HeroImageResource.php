<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroImageResource\Pages;
use App\Models\HeroImage;
use App\Models\League;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class HeroImageResource extends Resource
{
    protected static ?string $model = HeroImage::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    // NO navigationIcon property at all → avoids missing-SVG errors

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Overlay Title')
                    ->maxLength(255),

                FileUpload::make('image_path')
                    ->label('Hero Image')
                    ->image()
                    ->disk('public')
                    ->directory('hero')
                    ->required(),

                Select::make('league_id')
                    ->label('Display On')
                    ->options(self::getDisplayOptions())
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Preview'),

                TextColumn::make('title')
                    ->label('Title')
                    ->limit(30),

                TextColumn::make('league_id')
                    ->label('Where')
                    ->formatStateUsing(fn ($state, HeroImage $record) =>
                        $record->league
                            ? ($record->league->parent_id ? 'Sub-League: ' : 'League: ')
                              . $record->league->name
                            : 'Home Page'
                    )
                    ->badge(fn ($state, HeroImage $record) =>
                        $record->league ? 'success' : 'primary'
                    ),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('league_id')
                    ->label('Filter By')
                    ->options(self::getDisplayOptions()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['league_id'] = $data['league_id'] ?: null;
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['league_id'] = $data['league_id'] ?: null;
        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHeroImages::route('/'),
            'create' => Pages\CreateHeroImage::route('/create'),
            'edit'   => Pages\EditHeroImage::route('/{record}/edit'),
        ];
    }

    private static function getDisplayOptions(): array
    {
        return ['' => 'Home Page']
            + League::all()
                ->mapWithKeys(fn (League $l) => [
                    $l->id => ($l->parent_id ? '↳ ' : '') . $l->name,
                ])
                ->toArray();
    }
}
