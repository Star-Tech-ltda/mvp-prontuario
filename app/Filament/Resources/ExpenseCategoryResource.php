<?php

namespace App\Filament\Resources;

use App\Enums\CostType;
use App\Filament\Clusters\ExpenseCluster;
use App\Filament\Resources\ExpenseCategoryResource\Pages;
use App\Filament\Resources\ExpenseCategoryResource\RelationManagers;
use App\Models\ExpenseCategory;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static ?string $cluster = ExpenseCluster::class;

    protected static SubNavigationPosition $subNavigationPosition = subNavigationPosition::Top;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Categorias';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'Categoria';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Select::make('cost_type')
                    ->label('Tipo de Custo')
                    ->options(collect(CostType::cases())->mapWithKeys(fn ($case)=> [$case->value=>$case->label()]))
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->required()
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
            'index' => Pages\ManageExpenseCategory::route('/'),
            'view' => Pages\ViewExpenseCategory::route('/{record}'),
        ];
    }
}
