<?php

namespace App\Filament\Resources;

use App\Enums\Sex;
use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Filament\Resources\PatientResource\RelationManagers\EvolutionsRelationsManager;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;
    protected static ?string $navigationGroup = 'Enfermagem';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 1;
    public static function getModelLabel(): string
    {
        return 'Paciente';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pacientes';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('birth_date'),
                Forms\Components\TextInput::make('cpf')
                    ->maxLength(14),
                Forms\Components\TextInput::make('sex'),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('internment_reason')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('internment_date'),
                Forms\Components\TextInput::make('internment_time'),
                Forms\Components\TextInput::make('internment_location')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bed')
                    ->maxLength(255),
                Forms\Components\TextInput::make('diagnosis')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->html()
                        ->formatStateUsing(fn ($state) => '<span class="font-bold">NOME:</span> <span class="text-green-600">' . $state . '</span>'),

                    Tables\Columns\TextColumn::make('sex')
                        ->html()
                        ->formatStateUsing(fn (Sex $state) => '<span class="font-bold">SEXO:</span> ' . $state->label()),
                     Tables\Columns\TextColumn::make('bed')
                        ->html()
                         ->formatStateUsing(fn ($state) => '<span class="font-bold">LEITO:</span> <span class="underline">' . $state . '</span>'),

                    Tables\Columns\TextColumn::make('internment_reason')
                        ->label('Motivo da Internação')
                        ->color('gray'),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([18, 36, 72, 'all'])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'view' => Pages\ViewPatient::route('/{record}'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
