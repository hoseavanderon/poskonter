<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DigitalProductResource\Pages;
use App\Models\DigitalProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DigitalProductResource extends Resource
{
    protected static ?string $model = DigitalProduct::class;

    protected static ?string $navigationGroup = 'Produk Digital';
    protected static ?string $navigationIcon = 'heroicon-o-device-tablet';
    protected static ?string $navigationLabel = 'Produk Digital';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Produk')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('code')
                ->label('Kode Produk')
                ->required()
                ->maxLength(50),

            Forms\Components\Select::make('digital_category_id')
                ->label('Kategori Digital')
                ->relationship('digitalCategory', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('base_price')
                ->label('Harga')
                ->required()
                ->maxLength(50),

            Forms\Components\Checkbox::make('is_fixed')
                ->label('Harga Tetap'),

            Forms\Components\Select::make('app_id')
                ->label('App Terkait')
                ->relationship('app', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\MultiSelect::make('digitalBrands')
                ->label('Digital Brands')
                ->relationship('digitalBrands', 'name') // relasi many-to-many
                ->preload()
                ->searchable(),
        ]);
    }

    // Table
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')->rowIndex(),
                Tables\Columns\TextColumn::make('name')->label('Nama Produk')->searchable(),
                Tables\Columns\TextColumn::make('digitalCategory.name')->label('Kategori'),
                Tables\Columns\TextColumn::make('base_price')->label('Harga Dasar'),
                Tables\Columns\IconColumn::make('is_fixed')
                    ->label('Harga Tetap')
                    ->boolean(),
                Tables\Columns\TextColumn::make('app.name')->label('App'),
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

    // Batasi akses: Admin tidak bisa akses
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

    // Pages
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDigitalProducts::route('/'),
            'create' => Pages\CreateDigitalProduct::route('/create'),
            'edit' => Pages\EditDigitalProduct::route('/{record}/edit'),
        ];
    }
}
