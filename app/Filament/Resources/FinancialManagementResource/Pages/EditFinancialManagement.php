<?php

namespace App\Filament\Resources\FinancialManagementResource\Pages;

use App\Filament\Resources\FinancialManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialManagement extends EditRecord
{
    protected static string $resource = FinancialManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
