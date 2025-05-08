<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use App\Filament\Resources\PatientResource\RelationManagers\EvolutionsRelationManager;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewPatients extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
    public function getRelationManagers(): array
    {
        return [
            EvolutionsRelationManager::class,
        ];
    }
}
