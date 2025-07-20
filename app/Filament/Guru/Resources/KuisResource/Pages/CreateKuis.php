<?php

namespace App\Filament\Guru\Resources\KuisResource\Pages;

use App\Filament\Guru\Resources\KuisResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
class CreateKuis extends CreateRecord
{
    protected static string $resource = KuisResource::class;
}
