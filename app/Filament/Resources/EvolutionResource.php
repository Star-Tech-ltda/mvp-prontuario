<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Evolutions;
use App\Filament\Clusters\ManagerPatients;
use App\Filament\Clusters\Patients;
use App\Filament\Resources\EvolutionResource\Pages;
use App\Filament\Resources\EvolutionResource\RelationManagers;
use App\Models\AssessmentGroup;
use App\Models\AssessmentOption;
use App\Models\Evolution;
use App\Models\Patient;
use App\Services\MetricInterpreterService;
use Carbon\Carbon;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class EvolutionResource extends Resource
{
    protected static ?string $model = Evolution::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Evoluções';

    protected static ?string $cluster = ManagerPatients::class;
    protected static SubNavigationPosition $subNavigationPosition = subNavigationPosition::Top;

    public static function form(Form $form): Form
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

            ]) ;
    }




    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Paciente')
                ->label('Paciente'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Realizado em')
                    ->dateTime()
                    ->sortable()
                  ,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvolutions::route('/'),
            'create' => Pages\CreateEvolution::route('/create'),
            'edit' => Pages\EditEvolution::route('/{record}/edit'),
        ];
    }

    public static function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function (Evolution $record, array $data) {
                    // Chamar seu service aqui
                    MetricInterpreterService::handle($data, $record->id);
                }),
        ];
    }
}
