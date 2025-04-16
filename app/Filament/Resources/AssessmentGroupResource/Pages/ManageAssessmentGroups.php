<?php

namespace App\Filament\Resources\AssessmentGroupResource\Pages;

use App\Filament\Resources\AssessmentGroupResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAssessmentGroups extends ManageRecords
{
    protected static string $resource = AssessmentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->slideOver('left'),
        ];
    }


}
