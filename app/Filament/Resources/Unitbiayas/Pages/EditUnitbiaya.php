<?php

namespace App\Filament\Resources\Unitbiayas\Pages;

use App\Filament\Resources\Unitbiayas\UnitbiayaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUnitbiaya extends EditRecord
{
    protected static string $resource = UnitbiayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
