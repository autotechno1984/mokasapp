<?php

namespace App\Filament\Resources\Unitbiayas\Pages;

use App\Filament\Resources\Unitbiayas\UnitbiayaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitbiayas extends ListRecords
{
    protected static string $resource = UnitbiayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
