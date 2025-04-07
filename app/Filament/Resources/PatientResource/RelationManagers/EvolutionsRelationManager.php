<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use App\Models\AssessmentGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class EvolutionsRelationManager extends RelationManager
{

    protected static string $relationship = 'Evolutions';

    public function canCreate(): bool
    {
        return true;
    }

    public function canCreateForRecord($ownerRecord): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
            ->createAnother(true),
        ];
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
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
                    ),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('observation')
            ->columns([
                Tables\Columns\TextColumn::make('observation'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
