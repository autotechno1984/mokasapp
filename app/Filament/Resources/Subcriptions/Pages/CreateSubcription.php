<?php

namespace App\Filament\Resources\Subcriptions\Pages;

use App\Filament\Resources\Subcriptions\SubcriptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubcription extends CreateRecord
{
    protected static string $resource = SubcriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
