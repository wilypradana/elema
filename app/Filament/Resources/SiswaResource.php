<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Filament\Resources\SiswaResource\RelationManagers;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Pengguna';
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Siswa';

    public static function getModelLabel(): string
    {
        return 'Siswa';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Siswa';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\TextInput::make('nis')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'l' => 'laki-laki',
                        'p' => 'perempuan',
                    ])
                    ->required(),
                Forms\Components\Select::make('id_kelas')
                    ->relationship('kelas', 'id')
                    ->default(null)
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        return $record->kode;
                    })
                    ,
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('nis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelas.nama'),
                Tables\Columns\TextColumn::make('kelas.angkatan.tahun'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jurusan')
                    ->relationship('kelas.jurusan', 'nama')
                    ->label('Filter Jurusan')
                    ->placeholder('Pilih Jurusan'),
                Tables\Filters\SelectFilter::make('angkatan ')
                    ->relationship('kelas.angkatan', 'tahun')
                    ->label('Filter Angkatan')
                    ->placeholder('Pilih Angkatan'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
