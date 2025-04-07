<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use App\Filament\Resources\PatientResource\RelationManagers\EvolutionsRelationManager;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPatients extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    public function getRelationManagers(): array
    {
        return [
            EvolutionsRelationManager::class,
        ];
    }
}
