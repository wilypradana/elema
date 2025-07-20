<?php

namespace App\Filament\Pages;

use App\Models\Guru;
use App\Models\MataPelajaran;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Str;

class GuruMataPelajaran extends Page implements HasTable
{
    use InteractsWithTable;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Guru Mata Pelajaran';
    protected static ?string $title = 'Manajemen Guru dan Mata Pelajaran';

    protected static string $view = 'filament.pages.guru-mata-pelajaran';

    protected static ?string $navigationGroup = 'Akademi';

    protected static ?int $navigationSort = 6;

    public $guru = null;
    public $mataPelajaran = [];
    public $mataPelajaranToRemove = [];

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Guru::with('mataPelajarans'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Guru')
                    ->searchable(),
                TextColumn::make('mata_pelajaran_list')
                    ->label('Mata Pelajaran')
                    ->getStateUsing(function (Guru $record) {
                        return $record->mataPelajarans->pluck('kode')->implode(', ');
                    })
                    ->badge()
                    ->color('primary')
                    ->separator(', ')
            ]);
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('guru')
                ->label('Pilih Guru')
                ->options(Guru::all()->pluck('name', 'id'))
                ->required()
                ->live()
                ->afterStateUpdated(function (callable $set) {
                    $set('mataPelajaran', []);
                    $set('mataPelajaranToRemove', []);
                }),
            Select::make('mataPelajaran')
                ->label('Tambah Mata Pelajaran')
                ->multiple()
                ->options(function ($get) {
                    $guruId = $get('guru');
                    if (!$guruId) return [];
                    
                    $existingMataPelajaran = Guru::find($guruId)?->mataPelajarans->pluck('id')->toArray() ?? [];
                    return MataPelajaran::whereNotIn('id', $existingMataPelajaran)->pluck('kode', 'id');
                })
                ->required(),
            Select::make('mataPelajaranToRemove')
                ->label('Hapus Mata Pelajaran')
                ->multiple()
                ->options(function ($get) {
                    $guruId = $get('guru');
                    if (!$guruId) return [];
                    
                    return Guru::find($guruId)?->mataPelajarans->pluck('kode', 'id') ?? [];
                })
        ];
    }

    public function save()
    {
        $this->validate([
            'guru' => 'required|exists:gurus,id',
        ]);

        $guru = Guru::findOrFail($this->guru);
        
        // Tambahkan mata pelajaran baru
        if (!empty($this->mataPelajaran)) {
            $this->validate([
                'mataPelajaran.*' => 'exists:mata_pelajarans,id'
            ]);
            // Buat array dengan nilai slug untuk setiap mata pelajaran
            $mataPelajaranWithSlug = [];
            foreach ($this->mataPelajaran as $mataPelajaranId) {
                $mataPelajaranWithSlug[$mataPelajaranId] = ['slug' => Str::random(10)];
            }

            // Simpan dengan slug ke tabel pivot
    $guru->mataPelajarans()->syncWithoutDetaching($mataPelajaranWithSlug);
        }
        
        // Hapus mata pelajaran yang dipilih
        if (!empty($this->mataPelajaranToRemove)) {
            $guru->mataPelajarans()->detach($this->mataPelajaranToRemove);
        }

        Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Mata Pelajaran berhasil diperbarui.')
            ->send();

        // Refresh the table
        $this->dispatch('refresh-table');
    }

    public function mount()
    {
        $this->form->fill();
    }
}
