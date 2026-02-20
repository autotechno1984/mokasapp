<?php

namespace App\Filament\Resources\Masterbarangs;

use App\Models\Kategori;
use App\Models\Masterbarang;
use App\Models\Merek;
use App\Models\Model;
use App\Models\Tipe;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use TallStackUi\View\Components\Form\Toggle;

class MasterbarangResource extends Resource
{
    protected static ?string $model = Masterbarang::class;

    protected static ?string $slug = 'masterbarangs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipe_id')
                    ->label('Tipe')
                    ->options(Tipe::where('isactive', true)->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('kategori_id')
                    ->label('Kategori')
                    ->options(Kategori::where('isactive', true)->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('merek_id')
                    ->label('Merek')
                    ->options(Merek::where('isactive', true)->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('model_id')
                    ->label('Model')
                    ->options(Model::where('isactive', true)->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),

                TextInput::make('nama_barang')
                    ->required(),

                \Filament\Forms\Components\Toggle::make('isactive')
                    ->label('Status Aktif')
                    ->default(true)
                    ->inline(false),




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
                TextColumn::make('tipe.nama')
                    ->label('Tipe'),

                TextColumn::make('kategori.nama')
                    ->label('Kategori'),

                TextColumn::make('merek.nama')
                    ->label('Merek'),

                TextColumn::make('model.nama')
                    ->label('Model'),

                TextColumn::make('nama_barang'),
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
            'index' => Pages\ListMasterbarangs::route('/'),
            'create' => Pages\CreateMasterbarang::route('/create'),
            'edit' => Pages\EditMasterbarang::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
