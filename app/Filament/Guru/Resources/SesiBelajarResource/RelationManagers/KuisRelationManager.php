<?php

namespace App\Filament\Guru\Resources\SesiBelajarResource\RelationManagers;

use App\Models\Kuis;
use Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KuisRelationManager extends RelationManager
{
    protected static string $relationship = 'kuis'; // relasi di model SesiBelajar
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('recordId') 
                    ->label('Pilih Kuis')
                    ->options(
                        Kuis::where('id_guru', Auth::user()->id)
                            ->pluck('judul', 'id')
                    )
                    ->searchable()
                    ->required(),
                        
                ]);
    }

   public function table(Table $table): Table
{
    return $table
        ->recordTitleAttribute('judul')
        ->columns([
            Tables\Columns\TextColumn::make('judul'),
        ])
        ->headerActions([
            Tables\Actions\AttachAction::make()
                ->label('Pilih Kuis')
                ->form([
                    Forms\Components\Select::make('recordId')
                        ->label('Pilih Kuis')
                        ->options(
                            fn () => Kuis::where('id_guru', Auth::id())
                                ->pluck('judul', 'id')
                        )
                        ->required(),
                ]),
        ])
        ->actions([
            Tables\Actions\DetachAction::make(),
            Tables\Actions\ViewAction::make("lihat")
                ->icon('heroicon-o-eye')
                ->url(fn (Kuis $record) =>
                    route('filament.guru.pages.view-quiz-result.{id}', ['id' => $record->slug]))
                ->tooltip('Lihat Hasil Kuis'),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DetachBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}

}
