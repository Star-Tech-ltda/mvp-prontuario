<?php

namespace App\Filament\Resources;

use App\Enums\Severity;
use App\Filament\Resources\AssessmentOptionResource\Pages;
use App\Filament\Resources\AssessmentOptionResource\RelationManagers;
use App\Models\AssessmentOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentOptionResource extends Resource
{
    protected static ?string $model = AssessmentOption::class;

    protected static ?string $navigationIcon = 'bx-select-multiple';
    protected static ?string $navigationGroup = 'Administração';

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
                Forms\Components\Select::make('assessment_group_id')
                    ->relationship('assessmentGroup', 'name')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('custom_phrase')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('severity')
                    ->required()
                ->options(collect(Severity::cases())->mapWithKeys(fn ($case)=> [$case->value=> $case->label()]))
                ->label('Gravidade'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assessmentGroup.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('severity'),
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
            'index' => Pages\ListAssessmentOptions::route('/'),
            'create' => Pages\CreateAssessmentOption::route('/create'),
            'edit' => Pages\EditAssessmentOption::route('/{record}/edit'),
        ];
    }
}
