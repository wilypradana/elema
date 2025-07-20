<?php

namespace App\Filament\Guru\Resources\PertanyaanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JawabansRelationManager extends RelationManager
{
    protected static string $relationship = 'jawabans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('jawaban')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('jawaban_benar')
                ->label('Jawaban Benar?')
                    ->required()
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jawaban')
            ->columns([
                Tables\Columns\TextColumn::make('jawaban'),
                Tables\Columns\IconColumn::make('jawaban_benar')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
