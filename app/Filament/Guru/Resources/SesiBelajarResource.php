<?php

namespace App\Filament\Guru\Resources;

use App\Filament\Guru\Resources\SesiBelajarResource\Pages;
use App\Filament\Guru\Resources\SesiBelajarResource\RelationManagers;
use App\Filament\Guru\Resources\SesiBelajarResource\RelationManagers\FileMaterisRelationManager;
use App\Filament\Guru\Resources\SesiBelajarResource\RelationManagers\KuisRelationManager;
use App\Filament\Guru\Resources\SesiBelajarResource\RelationManagers\MateriRelationManager;
use App\Filament\Guru\Resources\SesiBelajarResource\RelationManagers\TugasRelationManager;
use App\Models\GuruMataPelajaran;
use App\Models\Kuis;
use App\Models\MataPelajaran;
use App\Models\SesiBelajar;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SesiBelajarResource extends Resource
{
    protected static ?string $model = SesiBelajar::class;

    protected static ?string $navigationIcon = 'heroicon-s-puzzle-piece';
    protected static ?int $navigationSort = 1;  

    protected static bool $shouldRegisterNavigation = false;

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('id_guru_mata_pelajaran')
                            ->label("Mata Pelajaran")
                            ->options(function () {
                                // Mengambil data berdasarkan guru yang sedang login
                                return GuruMataPelajaran::where('id_guru', Auth::user()->id)
                                    ->get()
                                    ->pluck('mataPelajaran.nama', 'id');
                            })
                            ->required()
                            ->disabled()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guruMataPelajaran.mataPelajaran.nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d-m-Y'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('Kelola Sesi'),
                Tables\Actions\DeleteAction::make()
                ->label('Hapus Sesi'),
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
            MateriRelationManager::class,
            FileMaterisRelationManager::class,
            TugasRelationManager::class,
            KuisRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSesiBelajars::route('/'),
            'create' => Pages\CreateSesiBelajar::route('/create'),
            'edit' => Pages\EditSesiBelajar::route('/{record:slug}/edit')
        ];
    }
}
