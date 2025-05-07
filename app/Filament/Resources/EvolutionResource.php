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
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;


class EvolutionResource extends Resource
{
    protected static ?string $model = Evolution::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Evoluções';

    protected static ?string $cluster = ManagerPatients::class;
    protected static SubNavigationPosition $subNavigationPosition = subNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return 'Evolução';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Evoluções';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->is_admin) {
            $query->where('created_by', auth()->id());
        }
        return $query;
    }
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
                                           ->relationship('patient', 'name',
                                            modifyQueryUsing: fn ($query) => $query->where('created_by', auth()->id())
                                           )
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

                                    ]),

                                Section::make('Dados Biométricos')
                                    ->relationship('biometricData')
                                    ->schema([

                                        Section::make('Dados do paciente')->schema([
                                            Grid::make(2)->schema([
                                                TextInput::make('age')->label('Idade')->numeric()->readOnly(),
                                                Placeholder::make('diagnostico')
                                                    ->label('Diagnóstico')
                                                    ->content(fn ($get, $livewire) => $livewire->data['temp_diagnosis'] ?? 'Não possui HDM'),
                                            ])
                                        ]),

                                        Section::make('Pressão Arterial')->schema([
                                            Grid::make(2)->schema([
                                                TextInput::make('systolic_pressure')->label('Pressão Sistólica')->numeric(),
                                                TextInput::make('diastolic_pressure')->label('Pressão Diastólica')->numeric(),
                                            ])
                                        ]),

                                        Section::make('Frequências')->schema([
                                            Grid::make(3)->schema([
                                                TextInput::make('heart_rate')->label('Frequência Cardíaca')->numeric(),
                                                TextInput::make('respiratory_rate')->label('Frequência Respiratória')->numeric(),
                                                TextInput::make('oxygen_saturation')->label('Saturação de O2')->numeric(),
                                            ])
                                        ]),

                                        Section::make('Medidas Corporais')->schema([
                                            Grid::make(3)->schema([
                                                TextInput::make('height')->label('Altura (cm)')->numeric()->step(0.01),
                                                TextInput::make('weight')->label('Peso (kg)')->numeric()->step(0.01),
                                                TextInput::make('temperature')->label('Temperatura')->numeric(),
                                            ])
                                        ]),

                                    ])


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

                Tables\Columns\TextColumn::make('patient.internment_reason')
                ->label('Motivo da Internação'),

                TextColumn::make('evolution_text')
                    ->alignCenter()
                    ->label('Texto de Evolução')
                    ->icon('heroicon-o-clipboard')
                    ->copyable()
                    ->copyMessage('Texto copiado com sucesso!')
                    ->formatStateUsing(fn ()=> new HtmlString( '<span></span>'))

                  ,
            ])

            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\Action::make('View Information')
                    ->label('Detalhes')
                    ->color('slate')
                    ->icon('heroicon-s-eye')
                    ->infolist([
                        \Filament\Infolists\Components\Section::make('Evolução')
                            ->schema([
                                TextEntry::make('evolution_text')
                                    ->label(''),
                            ]),

                            TextEntry::make('created_at')
                            ->label('Realizada em:')
                            ->columns(),
                    ])
                ->modalSubmitAction(false)
                ->modalCancelAction(
                   fn($action)=>$action->label('Fechar')
                ),
                Tables\Actions\EditAction::make()->color('amber'),
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
            //
        ];
    }
}
