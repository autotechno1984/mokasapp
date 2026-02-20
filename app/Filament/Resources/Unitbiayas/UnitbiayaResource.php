<?php

namespace App\Filament\Resources\Unitbiayas;

use App\Models\Unitbiaya;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitbiayaResource extends Resource
{
    protected static ?string $model = Unitbiaya::class;

    protected static ?string $slug = 'unitbiayas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('unit_id')
                    ->required()
                    ->integer(),

                TextInput::make('kategori')
                    ->required(),

                TextInput::make('keterangan')
                    ->required(),

                TextInput::make('amount')
                    ->required()
                    ->numeric(),

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

                TextColumn::make('kategori'),

                TextColumn::make('keterangan'),

                TextColumn::make('amount'),
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
            'index' => Pages\ListUnitbiayas::route('/'),
            'create' => Pages\CreateUnitbiaya::route('/create'),
            'edit' => Pages\EditUnitbiaya::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
