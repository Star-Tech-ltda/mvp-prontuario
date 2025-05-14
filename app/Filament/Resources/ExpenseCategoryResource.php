<?php

namespace App\Filament\Resources;

use App\Enums\CostType;
use App\Filament\Clusters\ExpenseCluster;
use App\Filament\Resources\ExpenseCategoryResource\Pages\ManageExpenseCategory;
use App\Filament\Resources\ExpenseCategoryResource\Pages\ViewExpenseCategory;
use App\Filament\Resources\ExpenseCategoryResource\RelationManagers;
use App\Models\ExpenseCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Select::make('cost_type')
                    ->native(false)
                    ->label('Tipo de Custo')
                    ->required()
                    ->native(false)
                    ->options(collect(CostType::cases())->mapWithKeys(fn ($case)=> [$case->value=>$case->label()])),
                Textarea::make('description')
                    ->label('Descrição')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('cost_type')
                ->label('Tipo de Custo')
                    ->formatStateUsing(fn(CostType $state) => $state->label())
                ,
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
            'index' => ManageExpenseCategory::route('/'),
            'view' => ViewExpenseCategory::route('/{record}'),
        ];
    }
}
