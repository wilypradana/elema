<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use App\Filament\Imports\GuruImporter;
use App\Imports\GuruImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class ListGurus extends ListRecords
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('impor guru')
                ->label('Impor Guru')
                ->color('warning')
                ->icon('heroicon-o-document-arrow-down')
                ->form([
                    FileUpload::make('attachment')
                    ->label(new HtmlString('<a href="' . route('template-guru') . '" target="_blank" type="button" style="background-color: orange; color: white; padding: 2px;">Download Format Guru</a>'))

                ])
                ->action(function (array $data) {
                    $file = public_path('storage/' . $data['attachment']);
                    Excel::import(new GuruImport, $file);
                    Notification::make()
                        ->title('Sukses')
                        ->body('Data guru berhasil diimpor')
                        ->success()
                        ->send();
                    // Hapus file setelah diimpor
                    Storage::disk('public')->delete($data['attachment']);
                })
                ,
        ];
    }
}
