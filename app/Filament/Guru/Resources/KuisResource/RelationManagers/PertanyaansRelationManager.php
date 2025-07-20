<?php

namespace App\Filament\Guru\Resources\KuisResource\RelationManagers;

use App\Filament\Guru\Resources\PertanyaanResource;
use App\Filament\Imports\PertanyaanImporter;
use App\Imports\PertanyaansImport;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class PertanyaansRelationManager extends RelationManager
{
    protected static string $relationship = 'pertanyaans';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    RichEditor::make('pertanyaan')
                        ->required()
                        ->disableToolbarButtons([
                            'attachFiles',
                        ]),
                    Forms\Components\TextInput::make('bobot')
                        ->required(),
                ])
            ])
        ;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pertanyaan')
            ->columns([
                Tables\Columns\TextColumn::make('pertanyaan')
                    ->formatStateUsing(fn(string $state) => strip_tags($state)) // hilangkan tag HTML
                    ->limit(50), // jika ingin potong teks panjang,
                Tables\Columns\TextColumn::make('bobot')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // Tambahkan custom action untuk import
                Tables\Actions\Action::make('importSoal')
                    ->label('Import Soal', )
                    ->color('warning')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (array $data) {
                        if (isset($data['file'])) {
                            try {
                                $filePath = Storage::disk('public')->path($data['file']);
                                // Dapatkan id kuis dari URL atau dari record yang sedang di-edit
                                $kuisId = $this->getOwnerRecord()->id; // Mengambil dari Filament record
                                // Import file menggunakan path dan id kuis dari record
                                Excel::import(new PertanyaansImport($kuisId), $filePath);
                                Storage::disk('public')->delete($data['file']);
                                Notification::make()
                                    ->title('Berhasil upload')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Storage::disk('public')->delete($data['file']);
                                Notification::make()
                                    ->title('Gagal upload')
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            Storage::disk('public')->delete($data['file']);
                            Notification::make()
                                ->title('Gagal upload file tidak terbaca')
                                ->danger()
                                ->send();
                        }
                    })
                    ->form([
                        FileUpload::make('file')
                            ->disk('public')
                            ->visibility('public')
                            ->label(new HtmlString('<a href="' . route('template-soal') . '" target="_blank" type="button" style="background-color: orange; color: white; padding: 2px; border-radius: 5%;">Download Format Soal</a>'))
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel', // .xls
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                            ])
                            ->required(),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()->url(fn(Model $record): string => PertanyaanResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
