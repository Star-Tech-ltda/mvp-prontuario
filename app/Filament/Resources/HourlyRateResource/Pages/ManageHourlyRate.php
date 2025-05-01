<?php

namespace App\Filament\Resources\HourlyRateResource\Pages;

use App\Filament\Resources\HourlyRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHourlyRate extends ManageRecords
{
    protected static string $resource = HourlyRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->slideOver('left'),
        ];
    }
}
