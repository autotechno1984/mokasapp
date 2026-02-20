<?php

namespace App\Filament\Resources\Plans;

use App\Models\Plan;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $slug = 'plans';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Kode')
                    ->required(),

                TextInput::make('nama')
                    ->required(),

                TextInput::make('harga_bulanan')
                    ->required()
                    ->numeric(),

                TextInput::make('harga_tahunan')
                    ->required()
                    ->numeric(),

                TextInput::make('max_user')
                    ->required()
                    ->integer(),

                TextInput::make('max_cabang')
                    ->required()
                    ->integer(),

                CheckboxList::make('fitur')
                    ->options([
                        'pos' => 'Point of Sale',
                        'inventory' => 'Manajemen Inventory',
                        'laporan' => 'Laporan Keuangan',
                        'crm' => 'CRM',
                        'multi_cabang' => 'Multi Cabang',
                    ])
                    ->columns(2),

                Checkbox::make('is_active'),

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
                TextColumn::make('Kode'),

                TextColumn::make('nama'),

                TextColumn::make('harga_bulanan'),

                TextColumn::make('harga_tahunan'),

                TextColumn::make('max_user'),

                TextColumn::make('max_cabang'),

                TextColumn::make('fitur')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pos' => 'Point of Sale',
                        'inventory' => 'Manajemen Inventory',
                        'laporan' => 'Laporan Keuangan',
                        'crm' => 'CRM',
                        'multi_cabang' => 'Multi Cabang',
                        default => $state,
                    }),

                TextColumn::make('is_active'),
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
