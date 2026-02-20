<?php

namespace App\Filament\Resources\Tipes\Pages;

use App\Filament\Resources\Tipes\TipeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipe extends EditRecord
{
    protected static string $resource = TipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['nama'] = strtoupper($data['nama']);
        return $data;
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
