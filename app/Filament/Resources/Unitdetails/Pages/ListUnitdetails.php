<?php

namespace App\Filament\Resources\Unitdetails\Pages;

use App\Filament\Resources\Unitdetails\UnitdetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitdetails extends ListRecords
{
    protected static string $resource = UnitdetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
