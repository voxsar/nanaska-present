<?php

namespace App\Filament\Resources\PresentationEvaluationResource\Pages;

use App\Filament\Resources\PresentationEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPresentationEvaluation extends ViewRecord
{
    protected static string $resource = PresentationEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
