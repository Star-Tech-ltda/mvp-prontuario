<?php

namespace App\Filament\Resources\AssessmentGroupResource\RelationManagers;

use App\Enums\Severity;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessmentOptions';

    protected static ?string $title = 'Opções deste Grupo';
    protected static ?string $modelLabel = 'Opção';
    public function isReadOnly(): bool
    {
        return false; //ativar a criação de evoluções na página de visualização do paciente
    }

    public function form(Form $form): Form
    {
        return $form

            ->schema([
                Forms\Components\TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255),

                Select::make('severity')
                    ->required()
                    ->options(collect(Severity::cases())->mapWithKeys(fn ($case)=> [$case->value=> $case->label()]))
                    ->label('Gravidade')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('description')
                ->label('Descrição'),

                Tables\Columns\BadgeColumn::make('severity')
                    ->label('Gravidade')
                    ->formatStateUsing(fn (?Severity $state) => $state?->label())
                    ->color(fn (?Severity $state) => $state?->color())
                    ->toggleable(),

                tables\Columns\TextColumn::make('created_at')
                ->label('Criado em')
                ->toggleable(),
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
