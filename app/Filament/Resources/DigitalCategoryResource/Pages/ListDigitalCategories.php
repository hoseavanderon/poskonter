<?php

namespace App\Filament\Resources\DigitalCategoryResource\Pages;

use App\Filament\Resources\DigitalCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDigitalCategories extends ListRecords
{
    protected static string $resource = DigitalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
