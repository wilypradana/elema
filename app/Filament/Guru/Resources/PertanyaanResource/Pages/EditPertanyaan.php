<?php

namespace App\Filament\Guru\Resources\PertanyaanResource\Pages;

use App\Filament\Guru\Resources\PertanyaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPertanyaan extends EditRecord
{
    protected static string $resource = PertanyaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
