<?php

namespace App\Filament\Resources\Penjualans;

use App\Models\Penjualan;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $slug = 'penjualans';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tenant_id')
                    ->required()
                    ->integer(),

                TextInput::make('unit_id')
                    ->required()
                    ->integer(),

                DatePicker::make('tgl_jual'),

                TextInput::make('nama_konsumen')
                    ->required(),

                TextInput::make('alamat')
                    ->required(),

                TextInput::make('kontak')
                    ->required(),

                TextInput::make('harga_jual')
                    ->required()
                    ->numeric(),

                TextInput::make('status_pembelian')
                    ->required(),

                TextInput::make('leasing')
                    ->required(),

                TextInput::make('catatan')
                    ->required(),

                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant_id'),

                TextColumn::make('unit_id'),

                TextColumn::make('tgl_jual')
                    ->date(),

                TextColumn::make('nama_konsumen'),

                TextColumn::make('alamat'),

                TextColumn::make('kontak'),

                TextColumn::make('harga_jual'),

                TextColumn::make('status_pembelian'),

                TextColumn::make('leasing'),

                TextColumn::make('catatan'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
