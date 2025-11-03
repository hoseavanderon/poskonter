<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DigitalAdminRuleResource\Pages;
use App\Models\DigitalAdminRule;
use App\Models\DigitalCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DigitalAdminRuleResource extends Resource
{
    protected static ?string $model = DigitalAdminRule::class;

    protected static ?string $navigationGroup = 'Produk Digital';
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Digital Admin Rules';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('digital_category_id')
                ->label('Kategori Digital')
                ->relationship('digitalCategory', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('min_nominal')
                ->label('Minimal Nominal')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('max_nominal')
                ->label('Maksimal Nominal')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('admin_fee')
                ->label('Admin Fee')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')->rowIndex(),
                Tables\Columns\TextColumn::make('digitalCategory.name')->label('Kategori Digital')->sortable(),
                Tables\Columns\TextColumn::make('min_nominal')->label('Minimal Nominal'),
                Tables\Columns\TextColumn::make('max_nominal')->label('Maksimal Nominal'),
                Tables\Columns\TextColumn::make('admin_fee')->label('Admin Fee'),
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

    // Batasi akses untuk admin
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
            'index' => Pages\ListDigitalAdminRules::route('/'),
            'create' => Pages\CreateDigitalAdminRule::route('/create'),
            'edit' => Pages\EditDigitalAdminRule::route('/{record}/edit'),
        ];
    }
}
