<?php

namespace App\Providers;

use App\Models\BiometricData;
use App\Observers\BiometricDataObserver;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        EditAction::configureUsing(function (EditAction $action) {
            $action->color('amber'); // ou 'primary', 'warning', 'danger', etc.
        });



        Filament::serving(function () {
            App::setLocale('pt_BR');
        });

//        FilamentIcon::register([
//            'bx-select-multiple' => 'bx-select-multiple',//icone do AssessmentoptionResource
//        ], 'boxicons');
//        FilamentIcon::register([
//            'fluentui-form-multiple-48-o' => 'blade-fluentui-system-icons',
//        ], 'blade-fluentui-system-icons');
//        FilamentIcon::register([
//            'mdi-calendar-star-four-points' => 'mdi-calendar-star-four-points',
//        ], 'blade-mdi'); // icone do botao pra gerar data atual do form patient
//        FilamentIcon::register([
//            'mdi-clock-star-four-points-outline' => 'mdi-clock-star-four-points-outline',
//        ], 'blade-mdi'); // icone do botao pra gerar hora atual do form patient
        FilamentColor::register([
           'slate'=>Color::hex('#505050'),
           'amber' =>Color::hex('#f59e0b')
        ]);

    }


}
