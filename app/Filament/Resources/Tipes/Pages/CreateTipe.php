<?php

namespace App\Filament\Resources\Tipes\Pages;

use App\Filament\Resources\Tipes\TipeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTipe extends CreateRecord
{
    protected static string $resource = TipeResource::class;

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
