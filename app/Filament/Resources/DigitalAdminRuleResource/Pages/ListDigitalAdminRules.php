<?php

namespace App\Filament\Resources\DigitalAdminRuleResource\Pages;

use App\Filament\Resources\DigitalAdminRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDigitalAdminRules extends ListRecords
{
    protected static string $resource = DigitalAdminRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
