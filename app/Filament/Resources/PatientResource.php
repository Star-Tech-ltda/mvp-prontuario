<?php

namespace App\Filament\Resources;

use App\Enums\MaritalStatus;
use App\Enums\Sex;
use App\Filament\Clusters\Evolutions;
use App\Filament\Clusters\ManagerPatients;
use App\Filament\Clusters\Patients;
use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers\EvolutionsRelationManager;
use App\Models\Patient;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';


    protected static ?string $cluster = ManagerPatients::class;
    protected static SubNavigationPosition $subNavigationPosition = subNavigationPosition::Top;


    public static function getModelLabel(): string
    {
        return 'Paciente';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pacientes';
    }

    public static function calculateAge(?string $date): string
    {
        if (! $date) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)
            ->diff(now())
            ->format('%y anos e %m meses');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->is_admin) {
           $query->where('created_by', auth()->id());
        }
        return $query;
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        return $data;
    }

    public static function form(Form $form): Form
    {
        return $form

            ->schema([

                Section::make('Identificação do Paciente')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        DatePicker::make('birth_date')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->displayFormat('d/m/Y')
                            ->label('Data de Nascimento')
                            ->reactive()
                            ->columnSpan(1),

                        Placeholder::make('calculated_age')
                            ->label('Idade')
                            ->content(fn ($get) => PatientResource::calculateAge($get('birth_date')))                        ,


                        Select::make('sex')
                            ->options(collect(Sex::cases())->mapWithKeys(fn ($case)=> [$case->value=>$case->label()]))
                            ->label('Sexo do Paciente'),


                        Select::make('responsible')
                            ->label('Acompanhado por :')
                            ->native(false)
                            ->options([
                                'Esposo'=>'Esposo',
                                'Esposa'=>'Esposa',
                                'Familiar'=>'Familiar',
                                'Cuidador'=>'Cuidador',
                                'Filho'=>'Filho',
                                'Filha'=>'Filha',
                            ]),

                        Select::make('movement')
                            ->label('Deambulação/Movimentação')
                            ->native(false)
                            ->options([
                                'Deambulando sem auxílio'=> ' Deambulando sem auxílio',
                                'Deambulando com auxílio'=>'Deambulando com auxílio',
                                'Sem deambular'=>'Sem deambular',
                                'Acamado'=>'Acamado',
                                'Em cadeira de rodas'=>'Em cadeira de rodas',
                                'Restrito ao leito'=>'Restrito ao leito',
                                'Repousando no leito'=>'Repousando no leito',
                                'Ativo'=>'Ativo',
                                'Hiperativo'=>'Hiperativo',
                                'Hipoativo'=>'Hipoativo',
                            ]),

                ])->columns(3),
                Section::make('Informações de internação ')
                    ->schema([
                        DatePicker::make('internment_date')
                            ->label('Dia de internação Hospitalar')
                            ->native(false)
                            ->reactive()
                            ->displayFormat('d/m/Y')
                            ->suffixAction(fn () =>
                            Action::make('hoje')
                                ->label('Hoje')
                                ->icon('mdi-calendar-star-four-points')
                                ->action(fn ($set) => $set('internment_date', now()->toDateString()))
                            ),

                        TextInput::make('complaints')
                            ->label('Queixa principal')
                            ->maxLength(255)
                            ,

                        TextInput::make('internment_reason')
                            ->label('Proveniente de ')
                            ->maxLength(255)
                            ->columnSpan(2)
                            ,


                        TextInput::make('internment_location')
                            ->label('Local da Internação')
                            ->maxLength(255),

                        TextInput::make('bed')
                            ->label('Leito')
                            ->maxLength(255),

                        TextInput::make('diagnosis')
                            ->label('Diagnóstico Médico - HDM')
                            ->maxLength(255)
                           ->columnSpan(2) ,
                    ])->columns(4),

            ]) ;
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
                            return '<span class="underline">' . $state . '</span>  <span class="text-gray-400 ">|Leito:</span> ' . ($bed ? $bed : '') ;
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
