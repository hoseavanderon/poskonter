<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DigitalCategoryResource\Pages;
use App\Models\DigitalCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DigitalCategoryResource extends Resource
{
    protected static ?string $model = DigitalCategory::class;

    // Sidebar Group: Digital Products
    protected static ?string $navigationGroup = 'Produk Digital';
    protected static ?string $navigationIcon = 'heroicon-o-device-tablet';
    protected static ?string $navigationLabel = 'Kategori Digital';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Kategori Digital')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')->rowIndex(),
                Tables\Columns\TextColumn::make('name')->label('Nama Kategori')->searchable(),
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

    // Jika ingin membatasi akses untuk admin
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
            'index' => Pages\ListDigitalCategories::route('/'),
            'create' => Pages\CreateDigitalCategory::route('/create'),
            'edit' => Pages\EditDigitalCategory::route('/{record}/edit'),
        ];
    }
}
