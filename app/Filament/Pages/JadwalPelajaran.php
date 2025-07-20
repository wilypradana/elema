<?php

namespace App\Filament\Pages;

use App\Models\Kelas;
use App\Models\GuruMataPelajaran;
use App\Models\TahunPelajaran;
use App\Models\JadwalPelajaran as ModelJadwalPelajaran;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JadwalPelajaran extends Page implements HasTable
{
    use InteractsWithTable;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Jadwal Pelajaran';
    protected static ?string $title = 'Manajemen Jadwal Pelajaran';
    protected static ?string $navigationGroup = 'Akademi';
    protected static string $view = 'filament.pages.jadwal-pelajaran';
    protected static ?int $navigationSort = 7;

    public $kelas = null;
    public $tahunPelajaran = null;
    public $hari = null;
    public $jadwalPelajaran = [];
    public $jadwalPelajaranToRemove = [];

    public $tahunAktif;
    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => ModelJadwalPelajaran::whereHas('tahunPelajaran', function ($query) {
                $query->where('aktif', true);
            })->with([
                'kelas',
                'tahunPelajaran',
                'guruMataPelajaran.mataPelajaran',
                'guruMataPelajaran.guru'
            ]))
            ->columns([
                TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->sortable(),
                TextColumn::make('hari')
                    ->label('Hari')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('mata_pelajaran')
                    ->label('Mata Pelajaran')
                    ->getStateUsing(function (ModelJadwalPelajaran $record) {
                        return $record->guruMataPelajaran->mataPelajaran->nama .
                            ' (' . $record->guruMataPelajaran->guru->name . ')';
                    }),
                TextColumn::make('tahun_pelajaran')
                    ->label("Tahun Pelajaran")
                    ->getStateUsing(function (ModelJadwalPelajaran $record) {
                        return $record->tahunPelajaran->nama;
                    })
            ])
            ->actions([
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                    ->label('Hapus Jadwal Pelajaran'),
                ])
            ])
            ->filters([
                SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama'),
                SelectFilter::make('hari')
                    ->label('Hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ])
            ]);
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('tahunPelajaran')
                ->label('Tahun Pelajaran')
                ->options(TahunPelajaran::all()->pluck('nama', 'id'))
                ->required()
                ->live()
                ->afterStateUpdated(function (callable $set) {
                    $set('hari', null);
                    $set('jadwalPelajaran', []);
                    $set('jadwalPelajaranToRemove', []);
                }),

            Select::make('kelas')
                ->label('Pilih Kelas')
                ->options(function ($get) {
                    $tahunPelajaranId = $get('tahunPelajaran');
                    return $tahunPelajaranId
                        ? Kelas::all()->pluck('nama', 'id')
                        : [];
                })
                ->required()
                ->live()
                ->afterStateUpdated(function (callable $set) {
                    $set('hari', null);
                    $set('jadwalPelajaran', []);
                    $set('jadwalPelajaranToRemove', []);
                }),

            Select::make('hari')
                ->label('Pilih Hari')
                ->options([
                    'Senin' => 'Senin',
                    'Selasa' => 'Selasa',
                    'Rabu' => 'Rabu',
                    'Kamis' => 'Kamis',
                    'Jumat' => 'Jumat',
                    'Sabtu' => 'Sabtu',
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function (callable $set) {
                    $set('jadwalPelajaran', []);
                    $set('jadwalPelajaranToRemove', []);
                }),

            Select::make('jadwalPelajaran')
                ->label('Tambah Mata Pelajaran')
                ->multiple()
                ->options(function ($get) {
                    $kelasId = $get('kelas');
                    $tahunPelajaranId = $get('tahunPelajaran');
                    $hari = $get('hari');

                    if (!$kelasId || !$tahunPelajaranId || !$hari)
                        return [];

                    // Ambil guru mata pelajaran yang belum terjadwal di hari tersebut
                    return GuruMataPelajaran::whereNotIn('id', function ($query) use ($kelasId, $tahunPelajaranId, $hari) {
                        $query->select('id_guru_mata_pelajaran')
                            ->from('jadwal_pelajarans')
                            ->where('id_kelas', $kelasId)
                            ->where('id_tahun_pelajaran', $tahunPelajaranId)
                            ->where('hari', $hari);
                    })
                        ->with(['mataPelajaran', 'guru'])
                        ->get()
                        ->mapWithKeys(function ($item) {
                            return [
                                $item->id => $item->mataPelajaran->nama .
                                    ' - ' . $item->guru->name
                            ];
                        });
                })
                ->required(),


            Select::make('jadwalPelajaranToRemove')
                ->label('Hapus Mata Pelajaran')
                ->multiple()
                ->options(function ($get) {
                    $kelasId = $get('kelas');
                    $tahunPelajaranId = $get('tahunPelajaran');
                    $hari = $get('hari');

                    if (!$kelasId || !$tahunPelajaranId || !$hari)
                        return [];

                    return ModelJadwalPelajaran::where('id_kelas', $kelasId)
                        ->where('id_tahun_pelajaran', $tahunPelajaranId)
                        ->where('hari', $hari)
                        ->with(['guruMataPelajaran.mataPelajaran', 'guruMataPelajaran.guru'])
                        ->get()
                        ->mapWithKeys(function ($item) {
                            return [
                                $item->id => $item->guruMataPelajaran->mataPelajaran->kode .
                                    ' - ' . $item->guruMataPelajaran->guru->name
                            ];
                        });
                }),
        ];
    }

    public function save()
    {
        $this->validate([
            'kelas' => 'required|exists:kelas,id',
            'tahunPelajaran' => 'required|exists:tahun_pelajarans,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
        ]);

        DB::beginTransaction();
        try {
            // Tambah jadwal baru
            if (!empty($this->jadwalPelajaran)) {
                foreach ($this->jadwalPelajaran as $guruMataPelajaranId) {
                    ModelJadwalPelajaran::create([
                        'id_kelas' => $this->kelas,
                        'id_tahun_pelajaran' => $this->tahunPelajaran,
                        'id_guru_mata_pelajaran' => $guruMataPelajaranId,
                        'hari' => $this->hari,
                        'slug' => Str::random(10)
                    ]);
                }
            }

            // Hapus jadwal yang dipilih
            if (!empty($this->jadwalPelajaranToRemove)) {
                ModelJadwalPelajaran::whereIn('id', $this->jadwalPelajaranToRemove)->delete();
            }

            DB::commit();

            Notification::make()
                ->success()
                ->title('Berhasil')
                ->body('Jadwal pelajaran berhasil diperbarui.')
                ->send();

            // Refresh the table
            $this->dispatch('refresh-table');
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title('Gagal')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->send();
        }
    }

    public function mount()
    {
        $this->tahunAktif = TahunPelajaran::where('aktif', true)->first();
        $this->form->fill();
    }
}
