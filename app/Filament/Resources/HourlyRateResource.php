<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HourlyRateResource\Pages;
use App\Models\HourlyRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HourlyRateResource extends Resource
{
    protected static ?string $model = HourlyRate::class;

    protected static ?string $navigationLabel = 'Tarifa Horária';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Administração';

    public static function getModelLabel(): string
    {
        return 'Tarifa Horária';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('adjustment_percent')
                    ->label('Porcentagem de Ajuste (%)')
                    ->placeholder('ex: 20')
                    ->suffix('%')
                    ->required()
                    ->minValue(0)
                    ->numeric()
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('adjustment_percent')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ManageHourlyRate::route('/'),
        ];
    }
}
