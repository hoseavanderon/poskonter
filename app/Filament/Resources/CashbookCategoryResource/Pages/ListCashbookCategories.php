<?php

namespace App\Filament\Resources\CashbookCategoryResource\Pages;

use App\Filament\Resources\CashbookCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashbookCategories extends ListRecords
{
    protected static string $resource = CashbookCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
