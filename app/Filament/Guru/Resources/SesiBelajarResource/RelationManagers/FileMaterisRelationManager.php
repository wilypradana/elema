<?php

namespace App\Filament\Guru\Resources\SesiBelajarResource\RelationManagers;

use App\Models\FileMateri;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class FileMaterisRelationManager extends RelationManager
{
    protected static string $relationship = 'fileMateris';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    FileUpload::make('file')
                        ->disk('public')
                        ->visibility('public')
                        ->directory('filemateri')
                        ->storeFileNamesIn('nama')
                        ->maxSize(2048)
                        ->helperText('Ukuran maksimal file 2MB per file.')
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn(FileMateri $record) => Storage::url($record->file), true),
                Tables\Actions\DeleteAction::make()
                    ->action(function (FileMateri $record) {
                        Storage::disk('public')->delete($record->file);
                        $record->delete();
                    })
                    ->requiresConfirmation()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            foreach ($records as $record) {
                                Storage::disk('public')->delete($record->file);
                                $record->delete();
                            }
                            Notification::make()
                                ->title('Beberapa file berhasil dihapus')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->label('Hapus File'),
                ]),
            ]);
    }
}
