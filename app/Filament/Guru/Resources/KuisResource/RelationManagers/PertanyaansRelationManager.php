<?php

namespace App\Filament\Guru\Resources\KuisResource\RelationManagers;

use App\Filament\Guru\Resources\PertanyaanResource;
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
use Maatwebsite\Excel\Facades\Excel;

class PertanyaansRelationManager extends RelationManager
{
    protected static string $relationship = 'pertanyaans';

    public function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                RichEditor::make('pertanyaan')
                    ->required()
                    ->disableToolbarButtons(['attachFiles']),
                Forms\Components\TextInput::make('bobot')->required(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pertanyaan')
            ->columns([
                Tables\Columns\TextColumn::make('pertanyaan')
                    ->formatStateUsing(fn(string $state) => strip_tags($state))
                    ->limit(50),
                Tables\Columns\TextColumn::make('bobot'),
            ])
            ->headerActions([
            Tables\Actions\CreateAction::make(),

    // Tombol Template Soal 
    Tables\Actions\Action::make('templateSoal')
        ->label('Template Soal')
        ->icon('heroicon-o-link')
        ->color('gray')
        ->url('https://docs.google.com/spreadsheets/d/1-SMy0f8B1m_JyCzpWYaameG7PhrsdAyo28q99tXOai4/edit?usp=sharing', shouldOpenInNewTab: true),

    // Tombol Import Soal
    Tables\Actions\Action::make('importSoal')
        ->label('Import Soal')
        ->color('warning')
        ->icon('heroicon-o-document-arrow-down')
        ->action(function (array $data) {
            if (isset($data['file'])) {
                try {
                    $filePath = Storage::disk('public')->path($data['file']);
                    $kuisId = $this->getOwnerRecord()->id;
                    Excel::import(new PertanyaansImport($kuisId), $filePath);

                    if (Storage::disk('public')->exists($data['file'])) {
                        Storage::disk('public')->delete($data['file']);
                    }

                    Notification::make()
                        ->title('Berhasil upload')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    if (Storage::disk('public')->exists($data['file'])) {
                        Storage::disk('public')->delete($data['file']);
                    }

                    Notification::make()
                        ->title('Gagal upload: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            } else {
                Notification::make()
                    ->title('Gagal upload: File tidak terbaca')
                    ->danger()
                    ->send();
            }
        })
        ->form([
            FileUpload::make('file')
                ->disk('public')
                ->visibility('public')
                ->acceptedFileTypes([
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->required(),
        ])
])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn(Model $record): string => PertanyaanResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
