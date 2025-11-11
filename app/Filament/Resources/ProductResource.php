<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use App\Models\Shelf;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-s-cube';
    protected static ?string $navigationGroup = 'Produk';
    protected static ?string $navigationLabel = 'Produk';

    // Hanya user selain admin yang bisa akses
    public static function canViewAny(): bool
    {
        return Auth::user()->role !== 'Admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Product Name'),

                Forms\Components\TextInput::make('barcode')
                    ->label('Barcode')
                    ->unique(
                        table: Product::class,
                        column: 'barcode',
                        ignoreRecord: true,
                        modifyRuleUsing: function ($rule) {
                            $rule->where('outlet_id', Auth::user()->outlet_id);
                        },
                    )
                    ->helperText('Biarkan kosong untuk generate otomatis.')
                    ->afterStateHydrated(function ($component, $record) {
                        if (!$record || !$record->barcode) {
                            $component->state('');
                        }
                    })
                    ->dehydrateStateUsing(function ($state, callable $get) {
                        if (!empty($state)) {
                            return $state;
                        }

                        do {
                            $random = str_pad(mt_rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
                        } while (Product::where('barcode', $random)
                            ->where('outlet_id', Auth::user()->outlet_id)
                            ->exists()
                        );

                        return $random;
                    }),

                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(fn() => Category::query()
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->required(),

                Forms\Components\Select::make('sub_category_id')
                    ->label('Sub Category')
                    ->options(fn($get) => SubCategory::where('category_id', $get('category_id'))->pluck('name', 'id'))
                    ->required(),

                Forms\Components\Select::make('brand_id')
                    ->label('Brand')
                    ->options(fn() => Brand::query()
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('modal')
                    ->label('Modal')
                    ->required(),

                Forms\Components\TextInput::make('jual')
                    ->label('Selling Price')
                    ->required(),

                Forms\Components\TextInput::make('minimal_stok')
                    ->label('Minimal Stok')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::all()->pluck('name', 'id')),

                Forms\Components\Select::make('shelf_id')
                    ->label('Rak')
                    ->options(fn() => Shelf::query()
                        ->where('outlet_id', Auth::user()->outlet_id ?? null)
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Hidden::make('outlet_id')
                    ->default(fn() => Auth::user()->outlet_id),

                Forms\Components\Repeater::make('productAttributeValues')
                    ->label('Product Attributes')
                    ->relationship('productAttributeValues')
                    ->schema([
                        Forms\Components\Select::make('product_attribute_id')
                            ->label('Attribute')
                            ->options(fn() => ProductAttribute::pluck('name', 'id'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('attribute_type', ProductAttribute::find($state)?->data_type);
                            })
                            ->required(),

                        Forms\Components\Hidden::make('attribute_type')
                            ->reactive()
                            ->afterStateHydrated(function ($state, callable $set, $record) {
                                $set('attribute_type', $record?->productAttribute?->data_type ?? null);
                            }),

                        Forms\Components\TextInput::make('value_string')
                            ->label('Value')
                            ->visible(fn($get) => in_array($get('attribute_type'), ['text', 'string']))
                            ->afterStateHydrated(
                                fn($component, $get, callable $set) =>
                                $set('value_string', $get('attribute_value'))
                            ),

                        Forms\Components\TextInput::make('value_integer')
                            ->label('Value')
                            ->numeric()
                            ->visible(fn($get) => in_array($get('attribute_type'), ['number', 'integer']))
                            ->afterStateHydrated(
                                fn($component, $get, callable $set) =>
                                $set('value_integer', $get('attribute_value'))
                            ),

                        Forms\Components\DatePicker::make('value_date')
                            ->label('Value')
                            ->visible(fn($get) => in_array($get('attribute_type'), ['date', 'datetime']))
                            ->afterStateHydrated(
                                fn($component, $get, callable $set) =>
                                $set('value_date', $get('attribute_value'))
                            ),

                        Forms\Components\Hidden::make('attribute_value')
                            ->dehydrateStateUsing(function ($get) {
                                $type = $get('attribute_type');
                                return match ($type) {
                                    'text', 'string' => $get('value_string'),
                                    'number', 'integer' => $get('value_integer'),
                                    'date', 'datetime' => $get('value_date'),
                                    default => null,
                                };
                            }),

                        Forms\Components\TextInput::make('stok')
                            ->numeric()
                            ->label('Stok')
                            ->required(),

                        Forms\Components\Hidden::make('last_restock_date')
                            ->default(fn() => now()->toDateString())
                            ->dehydrated(),

                        Forms\Components\Hidden::make('last_sale_date')
                            ->default(null)
                            ->dehydrated(),

                        Forms\Components\Hidden::make('outlet_id')
                            ->default(fn() => Auth::user()?->outlet_id)
                            ->dehydrated(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->createItemButtonLabel('Tambah Attribute'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No')->rowIndex(),
                Tables\Columns\TextColumn::make('name')->label('Product Name')->searchable(),
                Tables\Columns\TextColumn::make('jual')
                    ->label('Harga')
                    ->searchable()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')->label('Brand'),
                Tables\Columns\TextColumn::make('category.name')->label('Category'),
                Tables\Columns\TextColumn::make('subCategory.name')->label('Sub Category'),
                Tables\Columns\TextColumn::make('stok')
                    ->label('Stok')
                    ->getStateUsing(function ($record) {
                        return optional(
                            $record->attributeValues
                                ->where('outlet_id', Auth::user()?->outlet_id)
                                ->first()
                        )->stok ?? '-';
                    }),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('outlet_id', Auth::user()->outlet_id);
    }
}
