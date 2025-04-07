<?php

namespace App\Filament\Resources\EvolutionResource\Pages;

use App\Filament\Resources\EvolutionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditEvolution extends EditRecord
{
    protected static string $resource = EvolutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
    {
        $record = $this->record;

        $state = $this->form->getState();

        $selectedOptionIds = collect($state)
            ->filter(fn($value, $key) => str_starts_with($key, 'assessment_options_group_'))
            ->flatMap(fn($options) => is_array($options) ? $options : [])
            ->unique()
            ->values()
            ->all();

        $record->assessmentOptions()->sync($selectedOptionIds);
    }
}
