<?php

namespace App\Filament\Resources\EvolutionResource\Pages;

use App\Filament\Resources\EvolutionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvolutions extends ListRecords
{
    protected static string $resource = EvolutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
