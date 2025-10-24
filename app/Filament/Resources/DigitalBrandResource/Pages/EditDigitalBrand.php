<?php

namespace App\Filament\Resources\DigitalBrandResource\Pages;

use App\Filament\Resources\DigitalBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDigitalBrand extends EditRecord
{
    protected static string $resource = DigitalBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
