<?php

namespace App\Filament\Resources\AssessmentOptionResource\Pages;

use App\Filament\Resources\AssessmentOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssessmentOption extends EditRecord
{
    protected static string $resource = AssessmentOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
