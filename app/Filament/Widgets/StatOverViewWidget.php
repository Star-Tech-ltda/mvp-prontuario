<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatOverViewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        return [
            Stat::make('Pacientes', Patient::where('created_by', $userId)->count())
                ->description('Total de Pacientes Cadastrados')
                ->icon('heroicon-o-document-currency-dollar'),
            Stat::make('Orçamentos', Budget::count())
                ->description('Total de Orçamentos Cadastrados')
                ->icon('heroicon-o-user-group'),
        ];
    }
}
