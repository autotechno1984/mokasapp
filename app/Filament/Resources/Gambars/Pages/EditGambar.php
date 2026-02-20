<?php

namespace App\Filament\Resources\Gambars\Pages;

use App\Filament\Resources\Gambars\GambarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGambar extends EditRecord
{
    protected static string $resource = GambarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
