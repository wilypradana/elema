<?php

namespace App\Filament\Guru\Resources;

use App\Filament\Guru\Resources\PertanyaanResource\Pages;
use App\Filament\Guru\Resources\PertanyaanResource\RelationManagers;
use App\Filament\Guru\Resources\PertanyaanResource\RelationManagers\JawabansRelationManager;
use App\Models\Pertanyaan;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PertanyaanResource extends Resource
{
    protected static ?string $model = Pertanyaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\Select::make('id_kuis')
                        ->label('kuis')
                        ->relationship('kuis', 'judul')
                        ->required(),
                    RichEditor::make('pertanyaan')
                        ->required()
                        ->disableToolbarButtons([
                            'attachFiles',
                        ]),
                    Forms\Components\TextInput::make('bobot')
                        ->required()
                        ->numeric(),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kuis.judul')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pertanyaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            JawabansRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPertanyaans::route('/'),
            'create' => Pages\CreatePertanyaan::route('/create'),
            'edit' => Pages\EditPertanyaan::route('/{record}/edit'),
        ];
    }
}
