<?php

namespace App\Filament\Resources\Units;

use App\Models\Unit;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $slug = 'units';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('masterbarang_id')
                    ->required()
                    ->integer(),

                DatePicker::make('tgl_beli'),

                DatePicker::make('tgl_jual'),

                TextInput::make('harga_beli')
                    ->required()
                    ->numeric(),

                TextInput::make('harga_jual')
                    ->required()
                    ->numeric(),

                TextInput::make('biaya')
                    ->required()
                    ->numeric(),

                TextInput::make('status')
                    ->required(),

                Checkbox::make('unit_titip'),

                TextInput::make('lokasi_barang')
                    ->required()
                    ->integer(),

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

                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('masterbarang_id'),

                TextColumn::make('tgl_beli')
                    ->date(),

                TextColumn::make('tgl_jual')
                    ->date(),

                TextColumn::make('harga_beli'),

                TextColumn::make('harga_jual'),

                TextColumn::make('biaya'),

                TextColumn::make('status'),

                TextColumn::make('unit_titip'),

                TextColumn::make('lokasi_barang'),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<Unit>
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name'];
    }

    /**
     * @param Unit $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user) {
            $details['User'] = $record->user->name;
        }

        return $details;
    }
}
