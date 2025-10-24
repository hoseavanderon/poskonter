<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutletResource\Pages;
use App\Models\Outlet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\QueryBuilder;

class OutletResource extends Resource
{
    protected static ?string $model = Outlet::class;

    protected static ?string $navigationGroup = 'Manajemen Toko';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Outlet';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Outlet')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('address')
                ->label('Alamat')
                ->required(),

            Forms\Components\TextInput::make('phone')
                ->label('Nomor Telepon')
                ->tel()
                ->required()
                ->maxLength(50),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(), 
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Outlet')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon'),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(40),
            ])
            ->filters([
                QueryBuilder::make('owner_id')
                    ->label('Hanya Outlet Saya')
                    ->query(fn ($query) => $query->where('owner_id', Auth::id())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // tombol delete per row
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // tombol delete untuk multi row
            ]);
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->role === 'Admin';
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->role === 'Admin';
    }

    public static function canEdit($record): bool
    {
        return Auth::check() && Auth::user()->role === 'Admin';
    }

    public static function canDelete($record): bool
    {
        return Auth::check() && Auth::user()->role === 'Admin';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->role === 'Admin';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOutlets::route('/'),
            'create' => Pages\CreateOutlet::route('/create'),
            'edit' => Pages\EditOutlet::route('/{record}/edit'),
        ];
    }
}
