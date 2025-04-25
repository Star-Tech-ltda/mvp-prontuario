<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ProcedureCluster;
use App\Filament\Resources\ProcedureCategoryResource\Pages;
use App\Filament\Resources\ProcedureCategoryResource\RelationManagers;
use App\Models\ProcedureCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcedureCategoryResource extends Resource
{
    protected static ?string $model = ProcedureCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Categoria de Procedimento';

    protected static ?string $pluralLabel = 'Categorias de Procedimentos';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return 'Categoria';
    }


    protected static ?string $cluster = ProcedureCluster::class;
    protected static SubNavigationPosition $subNavigationPosition = subNavigationPosition::Top;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nome')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cost_type')
                    ->label('Tipo de custo')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->label('Descrição')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cost_type'),
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
            'index' => Pages\ListProcedureCategories::route('/'),
            'create' => Pages\CreateProcedureCategory::route('/create'),
            'edit' => Pages\EditProcedureCategory::route('/{record}/edit'),
        ];
    }
}
