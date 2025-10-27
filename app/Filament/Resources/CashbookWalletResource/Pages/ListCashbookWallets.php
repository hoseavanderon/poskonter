<?php

namespace App\Filament\Resources\CashbookWalletResource\Pages;

use App\Filament\Resources\CashbookWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashbookWallets extends ListRecords
{
    protected static string $resource = CashbookWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
