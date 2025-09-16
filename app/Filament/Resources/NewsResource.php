<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\League;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
class NewsResource extends Resource
{
    protected static ?string $model = News::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // The slot picker drives visibility
            Select::make('position')
                ->label('Layout Slot')
                ->options([
                    'hero'         => 'Hero Banner',
                    'secondary-1'  => 'Secondary Left',
                    'secondary-2'  => 'Secondary Right',
                    'slot-1'       => 'Small Card 1',
                    'slot-2'       => 'Small Card 2',
                    'slot-3'       => 'Small Card 3',
                ])
                ->reactive()
                ->required(),
    
            // Hero‐only image
            FileUpload::make('hero_image')
                ->label('Hero Image')
                ->image()
                ->disk('public')
                ->directory('news/hero')
                ->visibility('public')
                ->required(fn (callable $get) => $get('position') === 'hero')
                ->hidden(fn (callable $get)   => $get('position') !== 'hero')
                ->dehydrated(fn (callable $get) => $get('position') === 'hero'),
    
            // Title
            TextInput::make('title')
                ->label('Title')
                ->maxLength(255)
                ->required(fn (callable $get) => $get('position') !== 'hero')
                ->hidden(fn (callable $get)   => $get('position') === 'hero')
                ->dehydrated(fn (callable $get) => $get('position') !== 'hero'),
    
            // Content
            RichEditor::make('content')
                ->label('Content')
                ->required(fn (callable $get) => $get('position') !== 'hero')
                ->hidden(fn (callable $get)   => $get('position') === 'hero')
                ->dehydrated(fn (callable $get) => $get('position') !== 'hero'),
    
            // League
            Select::make('league_id')
                ->label('League')
                ->options(League::pluck('name', 'id'))
                ->searchable()
                ->required(fn (callable $get) => $get('position') !== 'hero')
                ->hidden(fn (callable $get)   => $get('position') === 'hero')
                ->dehydrated(fn (callable $get) => $get('position') !== 'hero')
                ->placeholder('Home Page')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('league.name')
                    ->label('League')
                    ->sortable(),

                // Rebuilt slot column—no enum() call
                Tables\Columns\BadgeColumn::make('position')
                    ->label('Slot')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hero'        => 'Hero',
                        'secondary-1' => '2nd Left',
                        'secondary-2' => '2nd Right',
                        'slot-1'      => 'Small 1',
                        'slot-2'      => 'Small 2',
                        'slot-3'      => 'Small 3',
                        default       => $state,
                    })
                    ->colors([
                        'warning' => 'hero',
                        'primary' => ['secondary-1', 'secondary-2'],
                        'success' => ['slot-1', 'slot-2', 'slot-3'],
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('position')
                    ->label('Slot')
                    ->options([
                        'hero'        => 'Hero',
                        'secondary-1' => '2nd Left',
                        'secondary-2' => '2nd Right',
                        'slot-1'      => 'Small 1',
                        'slot-2'      => 'Small 2',
                        'slot-3'      => 'Small 3',
                    ]),
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
            'index'  => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit'   => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
