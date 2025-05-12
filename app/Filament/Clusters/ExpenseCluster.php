<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ExpenseCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static ?string $navigationLabel = 'Despesas Operacionais';

    protected static ?string $navigationGroup = 'Administração';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
}
