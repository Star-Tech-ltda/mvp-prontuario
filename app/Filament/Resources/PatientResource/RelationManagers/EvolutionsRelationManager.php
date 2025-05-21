<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use App\Models\AssessmentGroup;
use App\Models\AssessmentOption;
use App\Models\Evolution;
use App\Models\Patient;
use App\Services\MetricInterpreterService;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class EvolutionsRelationManager extends RelationManager
{
    protected static string $relationship = 'evolutions';


    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Evoluções deste Paciente';
    }

    public function isReadOnly(): bool
    {
        return false; //ativar a criação de evoluções na página de visualização do paciente
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
                    ->label('Nova evolução')
                    ->icon('mdi-plus')
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

                Tables\Actions\Action::make('viewInformation')
                    ->label('Detalhes')
                    ->color('slate')
                    ->icon('heroicon-s-eye')
                    ->form([

                        RichEditor::make('evolution_text')
                            ->label('Evolução')
                            ->disabled()
                            ->hiddenLabel()
                            ->columnSpanFull(),

                        TextInput::make('ai_question')
                            ->label('Pergunta para a IA sobre a evolução')
                            ->placeholder('EX: Qual plano de cuidado para esse paciente?')
                            ->suffixAction(
                                \Filament\Forms\Components\Actions\Action::make('gerarResposta')
                                    ->label('Gerar resposta')
                                    ->icon('heroicon-m-sparkles')
                                    ->action(function ($state, $livewire, $get, $set) {
                                        $evolutionText = $get('evolution_text') ?? '';
                                        $question = $state;

                                        $prompt = "Evolução do paciente: {$evolutionText}\nPergunta: {$question}\nResponda com um texto formatado em HTML para exibir em um editor visual. Use <p>, <ul>, <strong> e títulos como <h3> quando fizer sentido.";

                                        $resposta = Http::withToken(env('OPENAI_API_KEY'))
                                            ->post('https://api.openai.com/v1/chat/completions', [
                                                'model' => 'gpt-3.5-turbo',
                                                'messages' => [
                                                    ['role' => 'user', 'content' => $prompt],
                                                ],
                                            ])->json()['choices'][0]['message']['content'] ?? 'Erro ao gerar resposta.';

                                        $set('ai_suggestion', $resposta);
                                    })

                            )->helperText('Essa pergunta será enviada para a IA e a resposta será exibida na tela'),

                        Fieldset::make('Sugestão da IA')
                            ->schema([
                                RichEditor::make('ai_suggestion')
                                    ->label('Resposta da IA')
                                    ->hiddenLabel()
                                    ->columnSpanFull(),

                                \Filament\Forms\Components\Actions::make([
                                    \Filament\Forms\Components\Actions\Action::make('salvarSugestaoIa')
                                        ->label('Salvar sugestão')
                                        ->color('success')
                                        ->icon('heroicon-m-check-circle')
                                        ->action(function ($get, $livewire) {
                                            $sugestao = $get('ai_suggestion');
                                            $record = $livewire->getMountedTableActionRecord();

                                            if (! $record) {
                                                Notification::make()
                                                    ->title('Registro não encontrado.')
                                                    ->danger()
                                                    ->send();
                                                return;
                                            }

                                            $record->ai_suggestion = $sugestao;
                                            $record->save();

                                            Notification::make()
                                                ->title('Sugestão salva com sucesso!')
                                                ->success()
                                                ->send();
                                        })

                                ])
                                    ->alignEnd()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->fillForm(fn ($record) => [
                        'evolution_text' => $record->evolution_text,
                        'ai_suggestion' => $record->ai_suggestion,

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
