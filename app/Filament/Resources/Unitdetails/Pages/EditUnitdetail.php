<?php

namespace App\Filament\Resources\Unitdetails\Pages;

use App\Filament\Resources\Unitdetails\UnitdetailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUnitdetail extends EditRecord
{
    protected static string $resource = UnitdetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
