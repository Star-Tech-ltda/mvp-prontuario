<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentIcon;
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
        FilamentIcon::register([
            'bx-select-multiple' => 'bx-select-multiple',//icone do AssessmentoptionResource
        ], 'boxicons');
        FilamentIcon::register([
            'vaadin-form' => 'vaadin-form',
        ], 'vaadin-icons'); // <- tem que bater com o nome do set real


    }
}
