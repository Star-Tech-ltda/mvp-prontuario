<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\SubNavigationPosition;

class ManagerPatients extends Cluster
{
    protected static ?string $navigationIcon = 'fluentui-text-bullet-list-square-person-20-o';
    protected static ?string $navigationGroup = 'Enfermagem';
    protected static ?string $navigationLabel = 'Gestão de Pacientes';


}
