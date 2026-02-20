<?php

namespace App\Filament\Resources\Gambars\Pages;

use App\Filament\Resources\Gambars\GambarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGambars extends ListRecords
{
    protected static string $resource = GambarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
