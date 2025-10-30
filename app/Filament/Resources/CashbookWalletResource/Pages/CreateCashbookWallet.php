<?php

namespace App\Filament\Resources\CashbookWalletResource\Pages;

use App\Filament\Resources\CashbookWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;


class CreateCashbookWallet extends CreateRecord
{
    protected static string $resource = CashbookWalletResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['outlet_id'] = Auth::user()->outlet_id;
        return $data;
    }
}
