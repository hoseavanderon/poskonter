<?php

namespace App\Filament\Resources\CashbookCategoryResource\Pages;

use App\Filament\Resources\CashbookCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashbookCategory extends EditRecord
{
    protected static string $resource = CashbookCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
