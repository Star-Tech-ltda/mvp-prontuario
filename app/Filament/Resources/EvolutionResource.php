<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvolutionResource\Pages;
use App\Filament\Resources\EvolutionResource\RelationManagers;
use App\Models\AssessmentGroup;
use App\Models\AssessmentOption;
use App\Models\Evolution;
use App\Models\EvolutionChecklist;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class EvolutionResource extends Resource
{
    protected static ?string $model = Evolution::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Enfermagem';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Evoluções';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Forms\Components\DatePicker::make('date')
                    ->required(),

                Forms\Components\Textarea::make('observation')
                    ->label('Observações')
                    ->rows(3)
                    ->nullable(),

                Forms\Components\Section::make('Checklist de Avaliação')
                    ->schema(
                        AssessmentGroup::with('assessmentOptions')->get()->map(function ($group) {
                            return Forms\Components\Section::make($group->name)
                                ->schema([
                                    Forms\Components\CheckboxList::make("assessment_options_group_{$group->id}")
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




    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient_id.name')
                ->label('Paciente'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                  ,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
}
