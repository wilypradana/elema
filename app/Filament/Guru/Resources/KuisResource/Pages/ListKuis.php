<?php

namespace App\Filament\Guru\Resources\KuisResource\Pages;

use App\Filament\Guru\Resources\KuisResource;
use Auth;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKuis extends ListRecords
{
    protected static string $resource = KuisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    
}
