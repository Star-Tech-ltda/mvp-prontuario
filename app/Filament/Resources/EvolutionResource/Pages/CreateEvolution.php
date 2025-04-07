<?php

namespace App\Filament\Resources\EvolutionResource\Pages;

use App\Filament\Resources\EvolutionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateEvolution extends CreateRecord
{
    protected static string $resource = EvolutionResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        $state = $this->form->getState();

        Log::info('Estado do formulário:', $state);

        $selectedOptionIds = collect($state)
            ->filter(fn($value, $key) => str_starts_with($key, 'assessment_options_group_'))
            ->flatMap(fn($options) => is_array($options) ? $options : [])
            ->unique()
            ->values()
            ->all();

        Log::info('IDs selecionados para salvar:', $selectedOptionIds);

        $record->assessmentOptions()->sync($selectedOptionIds);
    }
}
