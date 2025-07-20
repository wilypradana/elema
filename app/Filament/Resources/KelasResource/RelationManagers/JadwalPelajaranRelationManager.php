<?php

namespace App\Filament\Resources\KelasResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\TahunPelajaran;
use App\Models\GuruMataPelajaran;

class JadwalPelajaranRelationManager extends RelationManager
{
    protected static string $relationship = 'jadwalPelajaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_tahun_pelajaran')
                    ->label('Tahun Pelajaran')
                    ->default(TahunPelajaran::where('aktif', true)->first()?->id)
                    ->options(TahunPelajaran::where('aktif', true)->pluck('nama', 'id'))
                    ->required()
                    ,

                Forms\Components\Select::make('id_guru_mata_pelajaran')
                    ->label('Guru Mata Pelajaran')
                    ->options(GuruMataPelajaran::with(['mataPelajaran', 'guru'])
                        ->get()
                        ->mapWithKeys(function ($item) {
                            return [
                                $item->id => $item->mataPelajaran->nama .
                                    ' - ' . $item->guru->name
                            ];
                        }))
                    ->required(),

                Forms\Components\Select::make('hari')
                    ->label('Hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        $tahunAktif = TahunPelajaran::where('aktif', true)->first();

        return $table
            ->recordTitleAttribute('hari')
            ->columns([
                Tables\Columns\TextColumn::make('hari')
                    ->label('Hari')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('guruMataPelajaran.mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable(),

                Tables\Columns\TextColumn::make('guruMataPelajaran.guru.name')
                    ->label('Guru')
                    ->searchable(),

            ])
            ->modifyQueryUsing(function ($query) use ($tahunAktif) {
                return $query->where('id_tahun_pelajaran', $tahunAktif?->id);
            })
            ->filters([
                Tables\Filters\SelectFilter::make('hari')
                    ->label('Hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                    ])
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