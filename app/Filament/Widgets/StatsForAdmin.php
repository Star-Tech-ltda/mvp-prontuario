<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Patient;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsForAdmin extends BaseWidget
{
    protected static ?int $sort = 3;
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Usuários', User::count())
                ->description('Total de Usuários Cadastrados')
                ->icon('heroicon-c-user-circle'),
        ];
    }
}
