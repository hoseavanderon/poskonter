<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashbookWalletResource\Pages;
use App\Models\CashbookWallet;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;

class CashbookWalletResource extends Resource
{
    protected static ?string $model = CashbookWallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Dompet Pembukuan';
    protected static ?string $navigationGroup = 'Pembukuan';
    protected static ?string $modelLabel = 'Dompet Pembukuan';
    protected static ?string $pluralModelLabel = 'Daftar Dompet Kas';
    protected static ?string $slug = 'cashbook-wallets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cashbook_wallet')
                    ->label('Nama Dompet')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('contoh: Main Wallet, Cash, Bank Account'),

                Forms\Components\Hidden::make('outlet_id')
                    ->default(fn () => Auth::user()->outlet_id)
                    ->dehydrated(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                // 🔒 Filter berdasarkan outlet user yang sedang login
                $query->where('outlet_id', Auth::user()->outlet_id ?? 0);
            })
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('cashbook_wallet')
                    ->label('Nama Dompet')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

     // Batasi akses supaya admin tidak bisa mengakses
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashbookWallets::route('/'),
            'create' => Pages\CreateCashbookWallet::route('/create'),
            'edit' => Pages\EditCashbookWallet::route('/{record}/edit'),
        ];
    }
}
