<?php

namespace App\Filament\Guru\Resources\SesiBelajarResource\Pages;

use App\Filament\Guru\Resources\SesiBelajarResource;
use App\Models\SesiBelajar;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditSesiBelajar extends EditRecord
{
    protected static string $resource = SesiBelajarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Kembali')
                ->label('Kembali')
                ->action('kembali')
                ->icon('heroicon-o-arrow-left')
                ->color(color: 'primary'),
        ];
    }

    public function kembali(){
        return redirect()->route('filament.guru.pages.mata-pelajaran.{slugGuruMapel}', [
            'slugGuruMapel' => $this->record->guruMataPelajaran->slug,
        ]);
    }

    public function getHeading(): string
    {
        return 'Kelola '.$this->record->judul;
    }

    public function getBreadcrumbs(): array
    {
        return [
        ];
    }
}
