<?php

namespace App\Filament\Guru\Resources\SesiBelajarResource\Pages;

use App\Filament\Guru\Resources\SesiBelajarResource;
use App\Models\Guru;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListSesiBelajars extends ListRecords
{
    protected static string $resource = SesiBelajarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        // Get the currently logged-in teacher's ID
        $guruId = Auth::id();

        // Filter sessions based on the logged-in teacher's ID
        return parent::getTableQuery()
            ->whereHas('guruMataPelajaran', function ($query) use ($guruId) {
                $query->where('id_guru', $guruId);
            });
    }

   
}
