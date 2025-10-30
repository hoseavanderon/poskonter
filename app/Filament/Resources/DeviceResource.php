<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use App\Models\App;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MultiSelect;
use Illuminate\Database\Eloquent\Builder;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationGroup = 'Digital Product';
    protected static ?string $navigationIcon = 'heroicon-o-device-tablet';
    protected static ?string $navigationLabel = 'Devices';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nama Device')
                ->required()
                ->maxLength(255),

            Textarea::make('notes')
                ->label('Catatan'),

            Select::make('icon')
                ->label('Pilih Icon')
                ->options([
                    'device-phone-mobile' => '📱 Smartphone (HP)',
                    'credit-card'         => '💳 Mesin EDC',
                ])
                ->searchable()
                ->hint('Pilih icon yang mewakili device ini')
                ->prefixIcon(fn ($state) => match ($state) {
                    'device-phone-mobile' => 'heroicon-o-phone',
                    'credit-card' => 'heroicon-o-credit-card',
                    default => 'heroicon-o-phone',
                }),

            MultiSelect::make('apps')
                ->label('Apps Terpasang')
                ->relationship('apps', 'name') 
                ->preload()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')->rowIndex(),
                Tables\Columns\TextColumn::make('name')->label('Nama Device')->searchable(),
                Tables\Columns\TextColumn::make('outlet.name')->label('Outlet')->sortable(),
                Tables\Columns\TextColumn::make('notes')->label('Catatan')->limit(50),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('outlet_id', Auth::user()->outlet_id);
    }

    // Batasi akses: Admin tidak bisa lihat / create / edit / delete
    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->role !== 'Admin';
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->role !== 'Admin';
    }

    public static function canEdit($record): bool
    {
        return Auth::check() && Auth::user()->role !== 'Admin';
    }

    public static function canDelete($record): bool
    {
        return Auth::check() && Auth::user()->role !== 'Admin';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->role !== 'Admin';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
