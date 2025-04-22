<?php

namespace App\Filament\Resources\TimeContextResource\Pages;

use App\Filament\Resources\TimeContextResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeContext extends EditRecord
{
    protected static string $resource = TimeContextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
