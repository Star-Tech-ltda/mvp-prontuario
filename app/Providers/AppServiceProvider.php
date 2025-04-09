<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

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
        Filament::serving(function () {
            App::setLocale('pt_BR');
        });

        FilamentIcon::register([
            'bx-select-multiple' => 'bx-select-multiple',//icone do AssessmentoptionResource
        ], 'boxicons');
        FilamentIcon::register([
            'vaadin-form' => 'vaadin-form',
        ], 'vaadin-icons'); //icone do AssessmentGroupResource

        FilamentColor::register([
           'slate'=>Color::hex('#505050')
        ]);

    }


}
