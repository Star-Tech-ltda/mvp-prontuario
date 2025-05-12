<?php

namespace App\Filament\Resources;

use App\Enums\Severity;
use App\Filament\Clusters\EvaluationsCluster;
use App\Filament\Resources\AssessmentOptionResource\Pages;
use App\Filament\Resources\AssessmentOptionResource\RelationManagers;
use App\Models\AssessmentGroup;
use App\Models\AssessmentOption;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AssessmentOptionResource extends Resource
{
    protected static ?string $model = AssessmentOption::class;

    protected static ?string $cluster = EvaluationsCluster::class;

    protected static SubNavigationPosition $subNavigationPosition = subNavigationPosition::Top;


    protected static ?string $navigationIcon = 'radix-dot-filled';


    public static function getModelLabel(): string
    {
        return 'Opção ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Opções por Grupo';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('assessment_group_id')
                    ->relationship('assessmentGroup', 'name')
                    ->required()
                    ->native(false)
                    ->debounce(500)
                    ->label('Grupo Pertencente')
                    ->afterStateUpdated(function (callable $set, $state) {
                        $grName = AssessmentGroup::find($state)?->name;
                        $set('Preview-title', mb_strtoupper($grName, 'UTF-8'));
                    }),


                TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->debounce(500)
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('Preview-option-description', mb_strtoupper($state, 'UTF-8') );
                    })
                    ->maxLength(255)
                    ->columnSpan(2),

                Select::make('severity')
                    ->native(false)
                    ->required()
                    ->options(collect(Severity::cases())->mapWithKeys(fn ($case)=> [$case->value=> $case->label()]))
                    ->label('Gravidade')
                    ->columnSpan(1),

                Placeholder::make('info-metric')
                    ->label('')
                    ->columnSpan(2)
                    ->content(new HtmlString(
                        '<p class="mt-6 text-sm text-gray-500">É essencial prestar atenção à gravidade que está sendo associada, pois ela será utilizada como métrica em relatórios</p>'
                    )),

                Section::make('Pré-visualização')
                    ->description('É assim que vai ficar no formulário')
                    ->schema([
                        Placeholder::make('Preview-title')
                            ->label('')
                            ->content(function ($get) {
                                $title = $get('Preview-title');
                                return $title ? new HtmlString('<h2 class="text-xl font-semibold">' . $title . '</h2>'): null;
                            })
                            ->extraAttributes(['class' => 'py-2']),
                        CheckboxList::make('fake-preview-checkbox')
                            ->options(function ($get) {
                                $desc = $get('Preview-option-description');
                                return $desc ? ['desc_key' => strtoupper($desc)] : [];
                            })
                            ->label('')
                            ->disabled(),

                    ]),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assessmentGroup.name')
                    ->label('Grupo Pertencente')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('severity')
                    ->label('Gravidade')
                    ->formatStateUsing(fn (?Severity $state) => $state?->label())
                    ->color(fn (?Severity $state) => $state?->color())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                 SelectFilter::make('assessment_group_id')
                ->relationship('assessmentGroup', 'name')
                ->label('Grupo Pertencente')
                ->multiple()
                ->preload(),
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
            'index' => Pages\ManageAssessmentOptions::route('/'),
        ];
    }
}
