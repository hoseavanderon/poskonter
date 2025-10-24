<?php

namespace App\Filament\Resources\DigitalBrandResource\Pages;

use App\Filament\Resources\DigitalBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDigitalBrands extends ListRecords
{
    protected static string $resource = DigitalBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
