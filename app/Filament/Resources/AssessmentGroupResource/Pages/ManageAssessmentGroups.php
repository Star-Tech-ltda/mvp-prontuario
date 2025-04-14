<?php

namespace App\Filament\Resources\AssessmentGroupResource\Pages;

use App\Filament\Resources\AssessmentGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAssessmentGroups extends ManageRecords
{
    protected static string $resource = AssessmentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->slideOver('left'),
        ];
    }


}
