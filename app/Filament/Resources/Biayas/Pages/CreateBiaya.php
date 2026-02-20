<?php

namespace App\Filament\Resources\Biayas\Pages;

use App\Filament\Resources\Biayas\BiayaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBiaya extends CreateRecord
{
    protected static string $resource = BiayaResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
