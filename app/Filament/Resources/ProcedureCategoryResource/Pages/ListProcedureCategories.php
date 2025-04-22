<?php

namespace App\Filament\Resources\ProcedureCategoryResource\Pages;

use App\Filament\Resources\ProcedureCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcedureCategories extends ListRecords
{
    protected static string $resource = ProcedureCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
