<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Filament\Resources\KelasResource\RelationManagers;
use App\Filament\Resources\KelasResource\RelationManagers\JadwalPelajaranRelationManager;
use App\Filament\Resources\KelasResource\RelationManagers\SiswasRelationManager;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Akademi';

    protected static ?string $navigationLabel = 'Kelas';
    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return 'Kelas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kelas';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\TextInput::make('kode')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('nama')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('id_jurusan')
                        ->required()
                        ->relationship('jurusan', 'nama')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('nama')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Forms\Components\TextInput::make('kode')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                        ]),
                    Forms\Components\Select::make('id_angkatan')
                        ->required()
                        ->relationship('angkatan', 'tahun')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('tahun')
                            ->required()
                            ->unique(ignoreRecord: true),
                        ]),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jurusan.nama')
                    ->sortable(),
                Tables\Columns\TextColumn::make('angkatan.tahun')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('siswas_count')
                    ->label('Jumlah Siswa')
                    ->counts('siswas'),
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
            SiswasRelationManager::class,
            JadwalPelajaranRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}
