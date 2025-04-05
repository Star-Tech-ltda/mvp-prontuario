<?php

namespace App\Filament\Resources\AssessmentOptionResource\Pages;

use App\Filament\Resources\AssessmentOptionResource;
use App\Models\AssessmentGroup;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentOptions extends ListRecords
{
    protected static string $resource = AssessmentOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        // Criar uma aba para cada grupo de avaliação dinamicamente
        AssessmentGroup::orderBy('name')->get()->each(function ($group) use (&$tabs) {
            $tabs[$group->id] = Tab::make($group->name)
                ->modifyQueryUsing(function ($query) use ($group) {
                    return $query->where('assessment_group_id', $group->id);
                });
        });

        return $tabs;
    }
}
