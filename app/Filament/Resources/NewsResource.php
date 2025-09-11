<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use App\Models\League;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;
    protected static ?string $navigationLabel = 'News';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // NOTE: Form uses Filament\Forms\Form, NOT Filament\Resources\Form
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),

            Forms\Components\RichEditor::make('content')
                ->required()
                ->fileAttachmentsDirectory('public/news')
                ->toolbarButtons([
                    'bold', 'italic', 'link', 'bullet', 'number', 'attachFiles',
                ]),

            Forms\Components\Select::make('league_id')
                ->label('Attach to League/Sub-league')
                ->options(League::all()->pluck('name', 'id'))
                ->placeholder('Home Page')
                ->nullable(),
        ]);
    }

    // NOTE: Table uses Filament\Tables\Table, NOT Filament\Resources\Table
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('league.name')->label('League')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
