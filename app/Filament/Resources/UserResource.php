<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Outlet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\QueryBuilder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Manajemen Toko';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'User';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                ->required(fn (string $context): bool => $context === 'create')
                ->maxLength(255)
                ->revealable(),

            Forms\Components\Select::make('role')
                ->label('Role')
                ->options([
                    'Admin' => 'Admin',
                    'Kasir' => 'Kasir',
                ])
                ->required(),

            Forms\Components\Select::make('outlet_id')
                ->label('Outlet')
                ->relationship('outlet', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'success' => 'Admin',
                        'warning' => 'Kasir',
                    ])
                    ->label('Role'),

                Tables\Columns\TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                QueryBuilder::make('outlet_filter')
                    ->label('Hanya User Outlet Saya')
                    ->query(fn ($query) => Auth::check() ? $query->where(function ($q) {
                        $user = Auth::user();

                        // Ambil semua outlet yang dimiliki admin login
                        $outletIds = Outlet::where('owner_id', $user->id)->pluck('id');

                        $q->where('id', $user->id)             // dirinya sendiri
                        ->orWhereIn('outlet_id', $outletIds); // semua user di outlet miliknya
                    }) : $query->whereRaw('0=1')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), 
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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

    // Tambahkan ini supaya menu hanya muncul untuk admin
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->role === 'Admin';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
