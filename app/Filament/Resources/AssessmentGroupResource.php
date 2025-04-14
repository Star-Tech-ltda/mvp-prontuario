<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentGroupResource\Pages;
use App\Filament\Resources\AssessmentGroupResource\RelationManagers;
use App\Filament\Resources\AssessmentGroupResource\RelationManagers\AssessmentOptionsRelationManager;
use App\Models\AssessmentGroup;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class AssessmentGroupResource extends Resource
{
    protected static ?string $model = AssessmentGroup::class;

    protected static ?string $pluralLabel = 'Grupos para avaliação';
    protected static ?string $label = 'Grupo';
    protected static ?string $navigationGroup = 'Administração';
protected static ?string $navigationIcon = 'fluentui-form-multiple-48-o';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome do Grupo')
                     ->helperText('')
                     ->debounce(200)
                     ->afterStateUpdated(
                        function (callable $set, $state) {
                            $set('Preview', strtoupper($state));
                        })
                    ->required()
                    ->maxLength(255)
                ,
                Placeholder::make('Obs')
                ->label('')

                ,
                Section::make('Pré-visualização')
                    ->description('É assim que vai ficar no formulário')
                        ->schema([
                            Placeholder::make('Preview')
                                ->label('')
                                ->content(function ($get) {
                                    return new HtmlString(
                                        '<h2 class="text-xl font-semibold ">' . strtoupper($get('Preview')) . '</h2>'
                                    );
                                })
                                ->extraAttributes(['class' => 'py-2']),
                            CheckboxList::make('')
                            ->options([
                                '1' => 'Opção 1',
                                '2' => 'Opção 2',
                                '3' => 'Opção 3',
                            ])->disabled()
                        ])->hiddenOn('view') // esconder da pag de visualização
                ,
                            Placeholder::make('info-text')
                                ->label('')
                                ->content(new HtmlString(
                                    '<p class="text-sm text-gray-500">Você pode cadastrar opções para esse grupo e outros em
                                            <a href="' . route('filament.admin.resources.assessment-options.index') . '"
                                               class="text-primary-600 hover:underline inline-flex items-center gap-1">
                                                OPÇÕES POR GRUPO
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M18 13v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1
                                                         2-2h6M15 3h6m0 0v6m0-6L10
                                                         14"
                                                 />
                                                </svg>
                                            </a>
                                        </p>'
                                ))->hiddenOn('view')

                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            AssessmentOptionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAssessmentGroups::route('/'),
            'view' => Pages\ViewAssessmentGroup::route('/{record}'),
        ];
    }
}
