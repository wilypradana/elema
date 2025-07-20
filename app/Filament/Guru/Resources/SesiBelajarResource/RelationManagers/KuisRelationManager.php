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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KuisRelationManager extends RelationManager
{
    protected static string $relationship = 'kuis';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('judul')
                    ->required()
                    ->maxLength(255),
            ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul')
            ->columns([
                Tables\Columns\TextColumn::make('judul'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                ->recordSelectOptionsQuery(fn (Builder $query) => $query->where('id_guru', Auth::id())),
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
            ])
            ;
    }
}
