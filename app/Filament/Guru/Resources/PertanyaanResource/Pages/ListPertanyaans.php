<?php

namespace App\Filament\Guru\Resources\PertanyaanResource\Pages;

use App\Filament\Guru\Resources\PertanyaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPertanyaans extends ListRecords
{
    protected static string $resource = PertanyaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
