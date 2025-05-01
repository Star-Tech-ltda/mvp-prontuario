<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ProcedureCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';


    public static function canAccess():bool
    {
        return auth()->user()->is_admin;
    }

}
