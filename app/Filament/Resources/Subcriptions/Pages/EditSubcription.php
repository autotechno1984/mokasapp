<?php

namespace App\Filament\Resources\Subcriptions\Pages;

use App\Filament\Resources\Subcriptions\SubcriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubcription extends EditRecord
{
    protected static string $resource = SubcriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
