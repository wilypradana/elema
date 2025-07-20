<?php

namespace App\Filament\Guru\Pages;

use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Tables\Columns;
class ListPengumpulanTugas extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.guru.pages.list-pengumpulan-tugas';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'listPengumpulanTugas/{idTugas}';
    public $tugas;
    public $pengumpulanTugas;
    public $idTugas;

    public $deadline;
    public $activeRelationManager;

    public function mount($idTugas): void
    {
        $this->idTugas = $idTugas;
        $this->tugas = Tugas::query()->where('id', $idTugas)->first();
        $this->pengumpulanTugas = PengumpulanTugas::query()->where('id_tugas', $idTugas)->get();
        $this->deadline = \Carbon\Carbon::parse($this->tugas->deadline);
        // Ambil parameter 'activeRelationManager' dari URL
        $this->activeRelationManager = session('activeRelationManager', route('filament.guru.resources.sesi-belajars.index')); // Ambil dari session, default jika kosong
        session()->forget('activeRelationManager'); // Hapus dari session setelah digunakan
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => PengumpulanTugas::where('id_tugas', $this->idTugas))
            ->columns([
                TextColumn::make('siswa.name'),
                TextColumn::make('siswa.kelas.nama'),
                TextColumn::make('nilai')
                ->badge(),
                TextColumn::make('filePengumpulanTugas.nama_file')
                    ->label('File Terkumpul')
                    ->formatStateUsing(function (PengumpulanTugas $record) {
                        // Ambil semua file terkait dengan pengumpulan tugas
                        return $record->filePengumpulanTugas->map(function ($file) {
                            return '<a href="' . Storage::url($file->file) . '" target="_blank">' . $file->nama_file . '</a>';
                        })->implode('<br>'); // Gabungkan menjadi string HTML dengan pemisah <br>
                    })
                    ->html(),   
                TextColumn::make('created_at')
                    ->label("Waktu Pengumpulan")
                    ->date('d/m/Y')
                    ->badge(),
                IconColumn::make('Tepat Waktu')
                ->getStateUsing(function (PengumpulanTugas $record){
                    $createdAt = \Carbon\Carbon::parse($record->created_at);
                    return $createdAt > $this->deadline;
                }) // the column requires a state to be passed to it
                ->icon(function(bool $state): string {
                    if($state){
                        return 'heroicon-m-x-circle';
                    }else{
                        return 'heroicon-m-check-badge';
                    }
                })
                ->trueColor('danger')
                ->falseColor('primary'),
                IconColumn::make('edit_nilai')
                    ->getStateUsing(fn() => true) // the column requires a state to be passed to it
                    ->icon(fn(bool $state): string => 'heroicon-m-pencil-square') // always show the 'edit' icon
                    ->label('')
                    ->action(
                        \Filament\Tables\Actions\Action::make('edit-nilai')
                            ->form([
                                TextInput::make('nilai')
                                ->numeric(),
                            ])
                            ->fillForm(fn($record) => [
                                'nilai' => $record->nilai,
                            ])
                            ->action(fn($record, $data) => $record->update($data))
                    ),
            ])
            ->filters([
                // Filter untuk mencari siswa berdasarkan nama
                \Filament\Tables\Filters\Filter::make('siswa_name')
                    ->label('Cari Nama Siswa')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('siswa_name')
                            ->label('Nama Siswa')
                            ->placeholder('Cari nama siswa...')
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when(
                        $data['siswa_name'],
                        fn(Builder $query, $name) =>
                        $query->whereHas('siswa', fn(Builder $query) => $query->where('name', 'like', "%{$name}%"))
                    )),
            ]);;
    }
}
