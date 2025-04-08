<?php

namespace App\Filament\Resources\EvolutionResource\Pages;

use App\Filament\Resources\EvolutionResource;
use App\Models\AssessmentGroup;
use App\Models\AssessmentOption;
use Filament\Actions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Form;
class EditEvolution extends EditRecord
{
    protected static string $resource = EvolutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mount($record): void
    {
        parent::mount($record);

        $selectedOptionIds = $this->record->assessmentOptions()->pluck('assessment_option_id')->toArray();

        $groupedOptions = AssessmentOption::whereIn('id', $selectedOptionIds)->get()->groupBy('assessment_group_id');

        $dataToFill = [];

        foreach ($groupedOptions as $groupId => $options) {
            $dataToFill["assessment_options_group_{$groupId}"] = $options->pluck('id')->toArray();
        }

        $this->form->fill($dataToFill);
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->label('Paciente')
                    ->required(),

                Textarea::make('observation')
                    ->label('Observações')
                    ->rows(3)
                    ->nullable(),

                Section::make('Checklist de Avaliação')
                    ->schema(
                        AssessmentGroup::with('assessmentOptions')->get()->map(function ($group) {
                            $fieldName = "assessment_options_group_{$group->id}";


                            return Section::make($group->name)
                                ->schema([
                                    CheckboxList::make($fieldName)
                                        ->label('Selecione as opções')
                                        ->options(
                                            $group->assessmentOptions->pluck('description', 'id')->toArray()
                                        )
                                ])
                                ->collapsible();
                        })->toArray()
                    )
            ]);
    }






}
