<?php

namespace App\Filament\Resources\FinancialManagementResource\Pages;

use App\Filament\Resources\FinancialManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinancialManagement extends ListRecords
{
    protected static string $resource = FinancialManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
