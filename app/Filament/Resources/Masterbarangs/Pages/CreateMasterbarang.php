<?php

namespace App\Filament\Resources\Masterbarangs\Pages;

use App\Filament\Resources\Masterbarangs\MasterbarangResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMasterbarang extends CreateRecord
{
    protected static string $resource = MasterbarangResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nama_barang'] = strtoupper($data['nama_barang']);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

}
