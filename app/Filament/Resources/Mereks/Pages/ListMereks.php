<?php

namespace App\Filament\Resources\Mereks\Pages;

use App\Filament\Resources\Mereks\MerekResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListMereks extends ListRecords
{
    protected static string $resource = MerekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('merek')->modalWidth(Width::ThreeExtraLarge),
        ];
    }
}
