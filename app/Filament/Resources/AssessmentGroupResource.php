<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentGroupResource\Pages;
use App\Filament\Resources\AssessmentGroupResource\RelationManagers;
use App\Filament\Resources\AssessmentGroupResource\RelationManagers\AssessmentOptionsRelationManager;
use App\Models\AssessmentGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentGroupResource extends Resource
{
    protected static ?string $model = AssessmentGroup::class;
    public static function getModelLabel(): string
    {
        return 'Grupo ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Grupos Para Evolução';
    }

    protected static ?string $navigationGroup = 'Administração';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
            AssessmentOptionsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessmentGroups::route('/'),
            'create' => Pages\CreateAssessmentGroup::route('/create'),
            'edit' => Pages\EditAssessmentGroup::route('/{record}/edit'),
        ];
    }
}
