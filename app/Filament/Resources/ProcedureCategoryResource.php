<?php

namespace App\Filament\Resources;

use App\Enums\CostType;
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

class ProcedureCategoryResource extends Resource
{
    protected static ?string $model = ProcedureCategory::class;

    protected static ?string $cluster = ProcedureCluster::class;

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
                    ->required()
                    ->label('Nome')
                    ->maxLength(255),
                Forms\Components\Select::make('cost_type')
                    ->label('Tipo de custo')
                    ->options(collect(CostType::cases())->mapWithKeys(fn ($case)=> [$case->value=>$case->label()]))
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
            'index' => Pages\ManageProcedureCategory::route('/'),
            'view' => Pages\ViewProcedureCategory::route('/{record}'),
        ];
    }
}
