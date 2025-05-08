<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use App\Models\AssessmentGroup;
use App\Models\AssessmentOption;
use App\Models\Evolution;
use App\Models\Patient;
use App\Services\MetricInterpreterService;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvolutionsRelationManager extends RelationManager
{
    protected static string $relationship = 'evolutions';


    public function hasCombinedRelationManagerTabsWithForm(): bool
    {
        return true;
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Sinais Vitais')
                        ->description('Dados do paciente')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                            Grid::make(1)
                                ->schema([
                                    Section::make('Dados do paciente')
                                        ->schema([
                                            Grid::make(2)->schema([
                                                TextInput::make('biometricData.age')
                                                    ->label('Idade')
                                                    ->numeric()
                                                    ->default(function ($livewire) {
                                                        return $livewire->ownerRecord->birth_date?->age;
                                                    }),

                                                Placeholder::make('diagnostico')
                                                    ->label('Diagnóstico')
                                                    ->content(function ($livewire) {
                                                        return $livewire->ownerRecord->diagnosis ?? 'Não possui HDM';
                                                    }),
                                            ]),
                                        ]),
                                ]),

                            Section::make('Pressão Arterial')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextInput::make('biometricData.systolic_pressure')
                                            ->label('Pressão Sistólica')
                                            ->numeric()
                                            ->suffix('mmHg'),
                                        TextInput::make('biometricData.diastolic_pressure')
                                            ->label('Pressão Diastólica')
                                            ->numeric()
                                            ->suffix('mmHg'),
                                    ])
                                ]),

                            Section::make('Frequências')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextInput::make('biometricData.heart_rate')
                                            ->label('Frequência Cardíaca')
                                            ->numeric()
                                            ->suffix('bpm')
                                            ->suffixIcon('mdi-heart-pulse'),
                                        TextInput::make('biometricData.respiratory_rate')
                                            ->label('Frequência Respiratória')
                                            ->numeric()->suffix('rpm')
                                            ->suffixIcon('mdi-lungs'),
                                        TextInput::make('biometricData.oxygen_saturation')
                                            ->label('Saturação de Oxigênio (%)')
                                            ->numeric()
                                            ->suffix('%/O2'),
                                    ])
                                ]),

                            Section::make('Medidas Corporais')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextInput::make('biometricData.height')
                                            ->label('Altura (cm)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->extraInputAttributes([
                                                'min' => 0,
                                            ])
                                            ->suffixIcon('mdi-human-male-height-variant'),

                                        TextInput::make('biometricData.weight')
                                            ->label('Peso (kg)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->extraInputAttributes([
                                                'min' => 0,
                                            ])
                                            ->suffixIcon('mdi-weight-kilogram'),

                                        TextInput::make('biometricData.temperature')
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('evolutions')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Realizado em:')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('evolution_text')
                    ->label('Resultado')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, $livewire): array {
                        $data['patient_id'] = $livewire->ownerRecord->id;

                        return $data;
                    })
                    ->after(function (Evolution $record, array $data) {
                        $this->handleAssessmentOptions($record, $data);
                        $this->handleBiometricData($record, $data);
                        $this->generateEvolutionText($record);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Evolution $record): array {
                        return $this->prepareFormDataForEdit($data, $record);
                    })
                    ->after(function (Evolution $record, array $data) {
                        $this->handleAssessmentOptions($record, $data);
                        $this->handleBiometricData($record, $data);
                        $this->generateEvolutionText($record);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function extractAssessmentOptionIds(array $data): array
    {
        return collect($data)
            ->filter(fn($value, $key) => str_starts_with($key, 'assessment_options_group_'))
            ->flatMap(fn($options) => is_array($options) ? $options : [])
            ->unique()
            ->values()
            ->all();
    }


    protected function handleAssessmentOptions(Evolution $record, array $data): void
    {
        $selectedOptionIds = $this->extractAssessmentOptionIds($data);
        $record->assessmentOptions()->sync($selectedOptionIds);
    }


    protected function handleBiometricData(Evolution $record, array $data): void
    {
        $biometricData = $data['biometricData'] ?? [];

        if ($record->biometricData) {
            $record->biometricData->update($biometricData);
        } else {
            $record->biometricData()->create($biometricData);
        }

        if (class_exists(MetricInterpreterService::class) && $record->biometricData) {
            MetricInterpreterService::handle(
                $record->biometricData->toArray(),
                $record->id
            );
        }
    }


    protected function generateEvolutionText(Evolution $record): void
    {
        $record->load([
            'assessmentOptions.assessmentGroup',
            'patient',
            'biometricData',
            'calculatedMetrics',
        ]);

        if (method_exists($record, 'generateEvolutionText')) {
            $record->updateQuietly([
                'evolution_text' => $record->generateEvolutionText(),
            ]);
        }
    }
    protected function prepareFormDataForEdit(array $data, Evolution $record): array
    {
        if ($record->biometricData) {
            $data['biometricData'] = $record->biometricData->toArray();
        }

        $selectedOptionIds = $record->assessmentOptions()->pluck('assessment_option_id')->toArray();
        $groupedOptions = AssessmentOption::whereIn('id', $selectedOptionIds)->get()->groupBy('assessment_group_id');

        foreach ($groupedOptions as $groupId => $options) {
            $data["assessment_options_group_{$groupId}"] = $options->pluck('id')->toArray();
        }

        return $data;
    }
}
