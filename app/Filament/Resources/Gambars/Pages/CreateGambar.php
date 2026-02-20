<?php

namespace App\Filament\Resources\Gambars\Pages;

use App\Filament\Resources\Gambars\GambarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGambar extends CreateRecord
{
    protected static string $resource = GambarResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
