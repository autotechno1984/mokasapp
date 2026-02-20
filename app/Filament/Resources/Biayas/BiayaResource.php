<?php

namespace App\Filament\Resources\Biayas;

use App\Models\Biaya;
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

class BiayaResource extends Resource
{
    protected static ?string $model = Biaya::class;

    protected static ?string $slug = 'biayas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tenant_id')
                    ->required()
                    ->integer(),

                TextInput::make('kategori')
                    ->required(),

                DatePicker::make('tanggal'),

                TextInput::make('keterangan')
                    ->required(),

                TextInput::make('jumlah')
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
                TextColumn::make('tenant_id'),

                TextColumn::make('kategori'),

                TextColumn::make('tanggal')
                    ->date(),

                TextColumn::make('keterangan'),

                TextColumn::make('jumlah'),
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
            'index' => Pages\ListBiayas::route('/'),
            'create' => Pages\CreateBiaya::route('/create'),
            'edit' => Pages\EditBiaya::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
