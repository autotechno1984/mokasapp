<?php

namespace App\Filament\Resources\Subcriptions\Pages;

use App\Filament\Resources\Subcriptions\SubcriptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubcriptions extends ListRecords
{
    protected static string $resource = SubcriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
