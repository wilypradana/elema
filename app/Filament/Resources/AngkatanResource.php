<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AngkatanResource\Pages;
use App\Filament\Resources\AngkatanResource\RelationManagers;
use App\Filament\Resources\AngkatanResource\RelationManagers\KelasRelationManager;
use App\Models\Angkatan;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AngkatanResource extends Resource
{
    protected static ?string $model = Angkatan::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected static ?string $navigationGroup = 'Akademi';
    
    protected static ?string $navigationLabel = 'Angkatan';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Angkatan';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Angkatan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->unique(ignoreRecord: true),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            KelasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAngkatans::route('/'),
            'create' => Pages\CreateAngkatan::route('/create'),
            'edit' => Pages\EditAngkatan::route('/{record}/edit'),
        ];
    }
}
