<?php

namespace App\Filament\Resources\Subcriptions;

use App\Models\Subcription;
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

class SubcriptionResource extends Resource
{
    protected static ?string $model = Subcription::class;

    protected static ?string $slug = 'subcriptions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('plan_id')
                    ->required()
                    ->integer(),

                TextInput::make('status')
                    ->required(),

                TextInput::make('mulai_at')
                    ->required(),

                TextInput::make('berakhir_at')
                    ->required(),

                TextInput::make('trial_ends_at')
                    ->required(),

                TextInput::make('harga')
                    ->required(),

                TextInput::make('is_auto_renew')
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

                TextColumn::make('plan_id'),

                TextColumn::make('status'),

                TextColumn::make('mulai_at'),

                TextColumn::make('berakhir_at'),

                TextColumn::make('trial_ends_at'),

                TextColumn::make('harga'),

                TextColumn::make('is_auto_renew'),
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
            'index' => Pages\ListSubcriptions::route('/'),
            'create' => Pages\CreateSubcription::route('/create'),
            'edit' => Pages\EditSubcription::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
