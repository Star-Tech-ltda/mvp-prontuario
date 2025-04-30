<?php

namespace App\Filament\Resources\EvolutionResource\Pages;

use App\Filament\Resources\EvolutionResource;
use App\Models\AssessmentGroup;
use App\Models\AssessmentOption;
use App\Models\Patient;
use App\Services\MetricInterpreterService;
use Filament\Actions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
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

        if ($record->biometricData()->exists()) {

            MetricInterpreterService::handle($record->biometricData->toArray(), $record->id);

        }

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Sinais Vitais')
                        ->description('Selecione corretamente o paciente')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                            Grid::make(1)
                                ->schema([
                                    Select::make('patient_id')
                                        ->relationship('patient', 'name')
                                        ->label('Paciente')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Selecione um paciente',
                                        ])
                                        ->live()
                                        ->suffixIcon('fluentui-person-12-o')
                                        ->afterStateUpdated(function (callable $set, $state) {
                                            if ($state) {
                                                $patient = Patient::find($state);

                                                $set('biometricData.age', $patient?->birth_date?->age);
                                                $set('temp_internment_reason', $patient?->internment_reason);
                                                $set('temp_diagnosis', $patient?->diagnosis);
                                            } else {
                                                $set('biometricData.age', null);
                                                $set('temp_internment_reason', null);
                                                $set('temp_diagnosis', null);
                                            }
                                        }),
                                    Section::make('Dados do paciente')
                                        ->relationship('biometricData')
                                        ->schema([
                                            Grid::make(3)->schema([
                                                TextInput::make('age')
                                                    ->label('Idade')
                                                    ->numeric()
                                                    ->readOnly(),
                                                Placeholder::make('motivo_internacao')
                                                    ->label('Motivo da Internação')
                                                    ->content(fn ($get, $livewire) => $livewire->data['temp_internment_reason'] ?? '---')
                                                    ->reactive(),

                                                Placeholder::make('diagnostico')
                                                    ->label('Diagnóstico')
                                                    ->content(fn ($get, $livewire) => $livewire->data['temp_diagnosis'] ?? 'Não possui HDM')
                                                    ->reactive(),
                                            ]),
                                        ]),
                                ]),

                            Section::make('Pressão Arterial')
                                ->relationship('biometricData')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextInput::make('systolic_pressure')
                                            ->label('Pressão Sistólica')
                                            ->numeric()
                                            ->suffix('mmHg'),
                                        TextInput::make('diastolic_pressure')
                                            ->label('Pressão Diastólica')
                                            ->numeric()
                                            ->suffix('mmHg'),
                                    ])
                                ]),

                            Section::make('Frequências')
                                ->relationship('biometricData')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextInput::make('heart_rate')
                                            ->label('Frequência Cardíaca')
                                            ->numeric()
                                            ->suffix('bpm')
                                            ->suffixIcon('mdi-heart-pulse')
                                        ,
                                        TextInput::make('respiratory_rate')
                                            ->label('Frequência Respiratória')
                                            ->numeric()->suffix('rpm')
                                            ->suffixIcon('mdi-lungs'),
                                        TextInput::make('oxygen_saturation')
                                            ->label('Saturação de Oxigênio (%)')
                                            ->numeric()
                                            ->suffix('%/O2'),
                                    ])
                                ]),

                            Section::make('Medidas Corporais')
                                ->relationship('biometricData')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextInput::make('height')
                                            ->label('Altura (cm)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->extraInputAttributes([
                                                'min' => 0,
                                            ])
                                            ->suffixIcon('mdi-human-male-height-variant'),


                                        TextInput::make('weight')->label('Peso (kg)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->extraInputAttributes([
                                                'min' => 0,
                                            ])
                                            ->suffixIcon('mdi-weight-kilogram'),


                                        TextInput::make('temperature')
                                            ->label('Temperatura (°C)')
                                            ->numeric()
                                            ->suffixIcon('mdi-temperature-celsius')
                                            ->extraInputAttributes([
                                                'min' => 0,
                                            ]),
                                    ])
                                ]),

                        ]),

                    Step::make('Checklist')
                        ->description('Selecione conforme o estado do Paciente')
                        ->schema([
                            ...AssessmentGroup::with('assessmentOptions')->get()->map(function ($group) {
                                return Section::make($group->name)
                                    ->schema([
                                        CheckboxList::make("assessment_options_group_{$group->id}")
                                            ->label('Selecione as opções')
                                            ->options(
                                                $group->assessmentOptions->pluck('description', 'id')->toArray()
                                            )
                                    ])
                                    ->collapsible();
                            })->toArray(),

                        ]),
                    Step::make('Final')
                        ->description('Finalize a evolução do paciente')
                        ->schema([
                            Textarea::make('observation')
                                ->label('Queixas e Observações')
                                ->rows(3)
                                ->nullable(),
                        ]),
                ])->columnSpan('full')

            ])  ;
    }


}
