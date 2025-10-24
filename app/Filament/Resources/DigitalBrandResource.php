<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DigitalBrandResource\Pages;
use App\Models\DigitalBrand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\FileUpload;

class DigitalBrandResource extends Resource
{
    protected static ?string $model = DigitalBrand::class;

    // Sidebar group dan label
    protected static ?string $navigationGroup = 'Digital Product';
    protected static ?string $navigationIcon = 'heroicon-o-device-tablet';
    protected static ?string $navigationLabel = 'Digital Brands';

    // Form
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Brand')
                ->required()
                ->maxLength(255),

            FileUpload::make('logo')
                ->label('Logo Aplikasi')
                ->image() // hanya izinkan gambar
                ->directory('apps') // folder penyimpanan di storage/app/public/apps
                ->maxSize(1024) // maksimum 1MB
                ->imageEditor() // aktifkan cropper bawaan Filament
                ->imagePreviewHeight('150') // preview tinggi 150px
                ->columnSpanFull() // full width
                ->hint('Unggah logo aplikasi (opsional). Format: JPG, PNG, WEBP'),
        ]);
    }

    // Table
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')->rowIndex(),
                Tables\Columns\TextColumn::make('name')->label('Nama Brand')->searchable(),
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

    // Batasi agar admin tidak bisa akses
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
            'index' => Pages\ListDigitalBrands::route('/'),
            'create' => Pages\CreateDigitalBrand::route('/create'),
            'edit' => Pages\EditDigitalBrand::route('/{record}/edit'),
        ];
    }
}
