<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ProcedureCluster;
use App\Filament\Resources\ProcedureResource\Pages;
use App\Filament\Resources\ProcedureResource\RelationManagers;
use App\Models\Procedure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProcedureResource extends Resource
{
    protected static ?string $model = Procedure::class;

    protected static ?string $cluster = ProcedureCluster::class;

    protected static SubNavigationPosition $subNavigationPosition = subNavigationPosition::Top;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Tipos de Procedimento';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return 'Tipo de Procedimento';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('procedure_category_id')
                    ->relationship('procedureCategory', 'name')
                    ->label('Categoria do Procedimento')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('default_price')
                    ->label('Valor Padrão')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('editable_price')
                    ->label('Valor Editável')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('procedureCategory.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('editable_price')
                    ->boolean(),
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
            'index' => Pages\ManageProcedure::route('/'),
        ];
    }
}
