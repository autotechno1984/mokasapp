<?php

namespace App\Filament\Resources\Mereks\Pages;

use App\Filament\Resources\Mereks\MerekResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMerek extends EditRecord
{
    protected static string $resource = MerekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
