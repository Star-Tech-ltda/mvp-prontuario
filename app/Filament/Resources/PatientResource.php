<?php

namespace App\Filament\Resources;

use App\Enums\MaritalStatus;
use App\Enums\Sex;
use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Filament\Resources\PatientResource\RelationManagers\EvolutionsRelationManager;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
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
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                DatePicker::make('birth_date')
                ->label('Data de Nascimento'),

                TextInput::make('cpf')
                    ->label('CPF')
                    ->maxLength(14),

                Select::make('marital_status')
                ->nullable()
                ->options(collect(MaritalStatus::cases())->mapWithKeys(fn ($case)=> [$case->value=>$case->label()]))
                ->label('Estado Civil'),

                Select::make('sex')
                ->options(collect(Sex::cases())->mapWithKeys(fn ($case)=> [$case->value=>$case->label()]))
                ->label('Sexo'),

                TextInput::make('responsible')
                ->label('Responsável'),

                TextInput::make('phone')
                    ->label('Telefone')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('address')
                    ->label('Endereço')
                    ->maxLength(255),

                TextInput::make('internment_reason')
                    ->label('Motivo da Internação')
                    ->maxLength(255),

                DatePicker::make('internment_date'),

                Forms\Components\TimePicker::make('internment_time')
                ->label('Hora da Internação')
                ,

                TextInput::make('internment_location')
                    ->label('Local da Internação')
                    ->maxLength(255),

                TextInput::make('bed')
                    ->label('Leito')
                    ->maxLength(255),

                TextInput::make('diagnosis')
                    ->label('Diagnostico')
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
                        ->formatStateUsing(function ($state) {
                            return '<span class="font-bold text-lg text-black">' . $state . '</span>' ;
                        }),

                    Tables\Columns\TextColumn::make('sex')
                        ->size(10)
                        ->color('gray')
                        ->formatStateUsing(function (Sex $state, $record) {
                            $age=$record->birth_date?->age;
                            return $state->label() .','.' ' . ($age . ' anos');
                        }),


                    Tables\Columns\TextColumn::make('diagnosis')
                        ->size(12)
                        ->weight(FontWeight::Medium)
                        ->html()
                        ->formatStateUsing(function ($state, $record) {
                            $bed = $record->bed;
                            return '<span class="underline">' . $state . '</span>  <span class="text-gray-400 ">|</span> ' . ($bed ? $bed : '') ;
                        }),



                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([18, 36, 72, 'all'])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
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
            EvolutionsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'view' => Pages\ViewPatients::route('/{record}'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
