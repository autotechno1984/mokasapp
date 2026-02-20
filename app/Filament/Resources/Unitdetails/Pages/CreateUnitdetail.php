<?php

namespace App\Filament\Resources\Unitdetails\Pages;

use App\Filament\Resources\Unitdetails\UnitdetailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnitdetail extends CreateRecord
{
    protected static string $resource = UnitdetailResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
