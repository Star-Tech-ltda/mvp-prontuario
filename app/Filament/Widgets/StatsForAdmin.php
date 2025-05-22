<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Evolution;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
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
            Stat::make('Usuários ', User::count())
                ->description('Usuários cadastrados nos ultimos 7 dias')
                ->icon('heroicon-c-user-circle')
                ->chart(
                    collect(range(6, 0))
                    ->map(fn ($i) =>
                    User::whereDate('created_at', Carbon::today()->subDays($i))->count()
                    )
                        ->toArray()
                )
                ->color('success')
            ,
            Stat::make('Evoluções', Evolution::count())
                ->description('Evoluções feitas nos ultimos 7 dias')
                ->icon('heroicon-o-rectangle-stack')
                ->chart(
                    collect(range(6, 0))
                        ->map(fn ($i) =>
                        Evolution::whereDate('created_at', Carbon::today()->subDays($i))->count()
                        )
                        ->toArray()
                )
                ->color('info')
            ,
            Stat::make('Orçamentos', Budget::count())
                ->description('Orçamentos feitos nos ultimos 7 dias')
                ->icon('heroicon-o-document-currency-dollar')
                ->chart(
                    collect(range(6, 0))
                        ->map(fn ($i) =>
                        Budget::whereDate('created_at', Carbon::today()->subDays($i))->count()
                        )
                        ->toArray()
                )
                ->color('gray')
            ,
        ];
    }
}
