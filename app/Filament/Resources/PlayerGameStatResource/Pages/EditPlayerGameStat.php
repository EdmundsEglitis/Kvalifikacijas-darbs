<?php

namespace App\Filament\Resources\PlayerGameStatResource\Pages;

use App\Filament\Resources\PlayerGameStatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayerGameStat extends EditRecord
{
    protected static string $resource = PlayerGameStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
