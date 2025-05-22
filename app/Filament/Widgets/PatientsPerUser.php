<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class PatientsPerUser extends ChartWidget
{
    protected static ?string $heading = 'Pacientes por usuário';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function getData(): array
    {
        $data = Patient::join('users', 'patients.created_by', '=', 'users.id')
            ->selectRaw('patients.created_by, users.name, COUNT(*) as total')
            ->groupBy('patients.created_by', 'users.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $colors = [
            '#60A5FA', // azul
            '#34D399', // verde
            '#FBBF24', // amarelo
            '#F87171', // vermelho
            '#A78BFA', // roxo
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Pacientes',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderColor' => 'transparent',
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'cutout' => '0%',
        ];

    }
}
