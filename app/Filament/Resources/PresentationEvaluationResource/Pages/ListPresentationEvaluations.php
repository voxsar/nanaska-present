<?php

namespace App\Filament\Resources\PresentationEvaluationResource\Pages;

use App\Filament\Resources\PresentationEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresentationEvaluations extends ListRecords
{
    protected static string $resource = PresentationEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
