<?php

namespace App\Filament\Guru\Resources\KuisResource\Pages;

use App\Filament\Guru\Resources\KuisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKuis extends EditRecord
{
    protected static string $resource = KuisResource::class;

    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
