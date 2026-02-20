<?php

namespace App\Filament\Resources\Mereks\Pages;

use App\Filament\Resources\Mereks\MerekResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMerek extends CreateRecord
{
    protected static string $resource = MerekResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nama'] = strtoupper($data['nama']);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
