<?php

namespace App\Filament\Resources\AngkatanResource\Pages;

use App\Filament\Resources\AngkatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAngkatan extends EditRecord
{
    protected static string $resource = AngkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
