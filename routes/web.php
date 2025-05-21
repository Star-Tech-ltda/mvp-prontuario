<?php

use App\Http\Controllers\EvolutionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Artisan::command('logs:clear', function() {

    exec('rm -f ' . storage_path('logs/*.log'));

    exec('rm -f ' . base_path('*.log'));

    $this->comment('Logs have been cleared!');

})->describe('Clear log files');



Route::get('/evolution/download/{record}', [EvolutionController::class, 'downloadPdf'])->name('evolution.download');
