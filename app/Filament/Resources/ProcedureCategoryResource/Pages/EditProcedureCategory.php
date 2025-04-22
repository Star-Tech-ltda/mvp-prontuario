<?php

namespace App\Filament\Resources\ProcedureCategoryResource\Pages;

use App\Filament\Resources\ProcedureCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcedureCategory extends EditRecord
{
    protected static string $resource = ProcedureCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
