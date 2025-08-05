<?php

namespace App\Filament\Guru\Resources;

use App\Filament\Guru\Resources\KuisResource\Pages;
use App\Filament\Guru\Resources\KuisResource\RelationManagers;
use App\Filament\Guru\Resources\KuisResource\RelationManagers\PertanyaansRelationManager;
use App\Models\Kuis;
use Auth;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KuisResource extends Resource
{
    protected static ?string $model = Kuis::class;

    protected static ?string $navigationIcon = 'heroicon-s-fire';

    protected static ?int $navigationSort = 2;

    public $import_file;

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        if (Auth::user()) {
            $query->where('id_guru', Auth::id());
        }
        
        return $query;
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Kuis')->schema([
                    Forms\Components\Hidden::make('id_guru')
                        ->default(Auth::id()),
                    Forms\Components\TextInput::make('judul')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('deskripsi')
                        ->columnSpanFull(),
                    Forms\Components\DateTimePicker::make('waktu_mulai')
                        ->required()
                    ,
                    Forms\Components\DateTimePicker::make('waktu_selesai')
                        ->required()
                    ,
                    Forms\Components\TextInput::make('nilai_minimal')
                        ->required()
                        ->numeric()
                        ->minValue(0) 
                        ->maxValue(100) 
                        ->label('Nilai Minimal'),
                    Forms\Components\TextInput::make('durasi')
                        ->numeric()
                        ->label('Durasi Pengerjaan(menit)'),
                    Forms\Components\Toggle::make('acak_soal')
                        ->required()
                ])->columns(2)
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('durasi')
                    ->label("durasi (menit)"),
                Tables\Columns\TextColumn::make('nilai_minimal'),
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
            PertanyaansRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKuis::route('/'),
            'create' => Pages\CreateKuis::route('/create'),
            'edit' => Pages\EditKuis::route('/{record}/edit'),
        ];
    }
}
