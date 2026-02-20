<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_tenant')
                    ->required(),
                TextInput::make('subdomain')
                    ->required(),
                TextInput::make('jenis_usaha')
                    ->required()
                    ->default('showroom'),
                Select::make('status')
                    ->options(['active' => 'Active', 'suspend' => 'Suspend', 'trial' => 'Trial'])
                    ->default('active')
                    ->required(),
                Select::make('plan_id')
                    ->columnSpan(2)
                    ->relationship('plan', 'nama'),
                Section::make()
                    ->columnSpan(2)
                    ->schema([
                        KeyValue::make('settings')
                        ->keyLabel('Setting'),
                        KeyValue::make('data'),
                    ]),
            ]);
    }
}
