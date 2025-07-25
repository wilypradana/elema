<?php

namespace App\Filament\Guru\Resources\SesiBelajarResource\RelationManagers;

use App\Models\FilePengumpulanTugas;
use App\Models\PengumpulanTugas;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class TugasRelationManager extends RelationManager
{
    protected static string $relationship = 'tugas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\TextInput::make('judul')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\RichEditor::make('deskripsi')
                        ->maxLength(255)
                        ->disableToolbarButtons([
                            'attachFiles',
                            'attachImages'
                        ]),
                    Forms\Components\DateTimePicker::make('deadline')
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul')
            ->columns([
                Tables\Columns\TextColumn::make('judul'),
                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime()
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        // Menghapus file yang terkait
                        $pengumpulanTugas = PengumpulanTugas::where('id_tugas', $record->id)->first();
                        $filePengumpulanTugas = FilePengumpulanTugas::where('pengumpulan_tugas_id', $pengumpulanTugas->id)->get();
                        foreach ($filePengumpulanTugas as $file) {
                            // Hapus file dari storage
                            if (Storage::disk('public')->exists($file->file)) {
                                Storage::disk('public')->delete($file->file);
                            }
                            // Hapus dari database
                            $file->delete();
                        }

                        // Hapus pengumpulan tugas dari database
                        $pengumpulanTugas->delete();

                        // Refresh halaman
                        $record->delete();
                        return redirect()->back();
                    }),
                Action::make('review')
                    ->icon('heroicon-o-eye')
                    ->url(function ($record) {
                        $activeRelationManager = url()->previous();
                        session(['activeRelationManager' => url()->previous()]); // Simpan di session
                        return route('filament.guru.pages.listPengumpulanTugas.{idTugas}', parameters: [
                            'idTugas' => $record->id
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
