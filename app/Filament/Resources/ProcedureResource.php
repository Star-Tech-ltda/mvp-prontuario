<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ProcedureCluster;
use App\Filament\Resources\ProcedureResource\Pages\ManageProcedure;
use App\Filament\Resources\ProcedureResource\RelationManagers;
use App\Models\Procedure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Leandrocfe\FilamentPtbrFormFields\Money;

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
                Select::make('procedure_category_id')
                    ->native(false)
                    ->relationship('procedureCategory', 'name')
                    ->label('Categoria do Procedimento')
                    ->required(),
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('default_price')
                    ->label('Valor Padrão')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric(),
                Toggle::make('editable_price')
                    ->label('Valor Editável')
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('procedureCategory.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('default_price')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('editable_price')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ManageProcedure::route('/'),
        ];
    }
}
