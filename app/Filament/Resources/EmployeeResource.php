<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use App\Models\Outlet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\QueryBuilder;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Manajemen Toko';
    protected static ?string $navigationLabel = 'Employee';

    // Only Admin can see this resource
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Employee')
                ->required()
                ->maxLength(255),

            Forms\Components\DatePicker::make('bekerja_sejak')
                ->label('Bekerja Sejak')
                ->required(),

            Forms\Components\FileUpload::make('foto')
                ->label('Foto')
                ->image()
                ->directory('employees'),

            Forms\Components\Select::make('outlet_id')
                ->label('Outlet')
                ->relationship('outlet', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->options(fn () => \App\Models\Outlet::where('owner_id', Auth::id())->pluck('name', 'id'))
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')->rowIndex(),
                Tables\Columns\ImageColumn::make('foto')->label('Foto')->disk('public'),
                Tables\Columns\TextColumn::make('name')->label('Nama Employee')->searchable(),
                Tables\Columns\TextColumn::make('outlet.name')->label('Outlet')->sortable(),
                Tables\Columns\TextColumn::make('bekerja_sejak')->label('Bekerja Sejak')->date(),
            ])
            ->filters([
                QueryBuilder::make('my_outlet')
                    ->label('Hanya Employee Outlet Saya')
                    ->query(fn ($query) => Auth::check()
                        ? $query->whereHas('outlet', fn ($q) => $q->where('owner_id', Auth::id()))
                        : $query->whereRaw('0=1')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
