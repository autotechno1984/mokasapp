<?php

namespace App\Filament\Resources\Unitdetails;

use App\Models\Unitdetail;
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

class UnitdetailResource extends Resource
{
    protected static ?string $model = Unitdetail::class;

    protected static ?string $slug = 'unitdetails';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('unit_id')
                    ->required()
                    ->integer(),

                TextInput::make('no_polisi')
                    ->required(),

                TextInput::make('no_mesin')
                    ->required(),

                TextInput::make('no_rangka')
                    ->required(),

                TextInput::make('tahun')
                    ->required()
                    ->integer(),

                TextInput::make('warna')
                    ->required(),

                TextInput::make('nama_bpkb')
                    ->required(),

                TextInput::make('alamat_bpkb')
                    ->required(),

                TextInput::make('no_bpkb')
                    ->required(),

                DatePicker::make('masa_berlaku_pajak'),

                DatePicker::make('masa_berlaku_stnk'),

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
                TextColumn::make('unit_id'),

                TextColumn::make('no_polisi'),

                TextColumn::make('no_mesin'),

                TextColumn::make('no_rangka'),

                TextColumn::make('tahun'),

                TextColumn::make('warna'),

                TextColumn::make('nama_bpkb'),

                TextColumn::make('alamat_bpkb'),

                TextColumn::make('no_bpkb'),

                TextColumn::make('masa_berlaku_pajak')
                    ->date(),

                TextColumn::make('masa_berlaku_stnk')
                    ->date(),
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
            'index' => Pages\ListUnitdetails::route('/'),
            'create' => Pages\CreateUnitdetail::route('/create'),
            'edit' => Pages\EditUnitdetail::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
