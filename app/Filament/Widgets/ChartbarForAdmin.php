<?php

namespace App\Filament\Widgets;

use App\Models\Evolution;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class ChartbarForAdmin extends ChartWidget
{
    protected static ?string $heading = 'Evoluções por usuário';

    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function getData(): array
    {
        $topUsers = Evolution::selectRaw('created_by, COUNT(*) as total')
            ->groupBy('created_by')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $labels = $topUsers->map(function ($row) {
            $user = User::find($row->created_by);
            return $user ? $user->name : 'Desconhecido';
        });

        $data = $topUsers->pluck('total');

        return [
            'labels' => $labels->toArray(),
            'datasets' => [
                [
                    'label' => 'Total de Evoluções',
                    'data' => $data->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                ],
            ],
        ];
    }


    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => [
                'y' => [
                    'ticks' => [
                        'precision' => 0,
                        'stepSize' => 1,
                    ],
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => \Illuminate\Support\Js::from("
                            function(context) {
                                return context.dataset.label + ': ' + parseInt(context.parsed.y);
                            }
                        "),
                    ],
                ],
            ],
        ];
    }


}
