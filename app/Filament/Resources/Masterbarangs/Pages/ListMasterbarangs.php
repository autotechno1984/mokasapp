<?php

namespace App\Filament\Resources\Masterbarangs\Pages;

use App\Filament\Resources\Masterbarangs\MasterbarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterbarangs extends ListRecords
{
    protected static string $resource = MasterbarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
