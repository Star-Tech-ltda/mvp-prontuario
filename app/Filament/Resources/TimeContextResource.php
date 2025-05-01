<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeContextResource\Pages;
use App\Models\TimeContext;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TimeContextResource extends Resource
{
    protected static ?string $model = TimeContext::class;

    protected static ?string $navigationLabel = 'Contextos de Horário';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function canAccess():bool
    {
      return auth()->user()->is_admin;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('adjustment_percent')
                    ->required()
                    ->numeric(),
            ]);
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
            'index' => Pages\ListTimeContexts::route('/'),
            'create' => Pages\CreateTimeContext::route('/create'),
            'edit' => Pages\EditTimeContext::route('/{record}/edit'),
        ];
    }
}
