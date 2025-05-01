<?php

namespace App\Filament\Resources\ProcedureCategoryResource\Pages;

use App\Filament\Resources\ProcedureCategoryResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProcedureCategory extends ManageRecords
{
    protected static string $resource = ProcedureCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->slideOver('left'),
        ];
    }
}
