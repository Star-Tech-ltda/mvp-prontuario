<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ProcedureCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationLabel = 'Procedimentos';

    protected static ?string $navigationGroup = 'Administração';

    public static function canAccess():bool
    {
        return auth()->user()->is_admin;
    }
}
