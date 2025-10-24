<?php

namespace App\Filament\Resources\DigitalCategoryResource\Pages;

use App\Filament\Resources\DigitalCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDigitalCategory extends EditRecord
{
    protected static string $resource = DigitalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
