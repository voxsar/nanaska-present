<?php

namespace App\Filament\Resources\PresentationResource\Pages;

use App\Filament\Resources\PresentationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPresentation extends ViewRecord
{
    protected static string $resource = PresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
