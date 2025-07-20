<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Imports\SiswaImporter;
use App\Filament\Resources\SiswaResource;
use App\Imports\SiswasImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;


class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('impor siswa')
                ->label('Impor Siswa')
                ->color('warning')
                ->icon('heroicon-o-document-arrow-down')
                ->form([
                    // Tombol untuk mendownload template
                    FileUpload::make('attachment')
                    ->label(new HtmlString('<a href="' . route('template-siswa') . '" target="_blank" type="button" style="background-color: orange; color: white; padding: 2px;">Download Format Guru</a>'))
                ])
                ->action(function (array $data) {
                    $file = public_path('storage/' . $data['attachment']);
                    Excel::import(new SiswasImport, $file);
                    Notification::make()
                        ->title('Sukses')
                        ->body('Data siswa berhasil diimpor')
                        ->success()
                        ->send();
                    // Hapus file setelah diimpor
                    Storage::disk('public')->delete($data['attachment']);
                })
                ,
        ];
    }
}
