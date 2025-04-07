<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    protected function hasRelationManagers(): bool
    {
        return true;
    }

    protected function hasFormActions(): bool
    {
        return true;
    }

}
