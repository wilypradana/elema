<?php

namespace App\Filament\Resources\AngkatanResource\RelationManagers;

use App\Filament\Resources\KelasResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KelasRelationManager extends RelationManager
{
    protected static string $relationship = 'kelas';

    protected static ?string $recordTitleAttribute = 'nama';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('id_jurusan')
                    ->relationship('jurusan', 'nama')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jurusan.nama')
                    ->label('Jurusan')
                    ->badge()
                    ->colors([
                        'primary' => fn($state) => $state === 'Akuntansi',
                        'success' => fn($state) => $state === 'Teknik Komputer Jaringan',
                        'warning' => fn($state) => $state === 'Teknik Audio Visual',
                        'danger' => fn($state) => $state === 'Administrasi Perkantoran',
                    ]),
                Tables\Columns\TextColumn::make('siswas_count')
                    ->label('Jumlah Siswa')
                    ->counts('siswas'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jurusan')
                    ->relationship('jurusan', 'nama')
                    ->label('Filter Jurusan'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_kelas')
                    ->label('Edit')
                    ->icon('heroicon-o-eye')
                    ->action(fn($record) => $this->redirect(route('filament.admin.resources.kelas.edit', $record))),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
