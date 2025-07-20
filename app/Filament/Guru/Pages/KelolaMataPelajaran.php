<?php

namespace App\Filament\Guru\Pages;

use App\Models\Guru;
use App\Models\GuruMataPelajaran;
use Filament\Pages\Page;
use App\Models\MataPelajaran;
use App\Models\SesiBelajar;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Http\RedirectResponse;

class KelolaMataPelajaran extends Page 
{
    // use InteractsWithTable;
    use InteractsWithFormActions;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.guru.pages.kelola-mata-pelajaran';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Kelola Mata Pelajaran';
    protected static ?string $slug = 'mata-pelajaran/{slugGuruMapel}'; // Custom URL slug
    public $guruMapel;
    public $mataPelajaran;
    public $judul;
    public $sesiBelajar;
    public $slugGuruMapel;
    public $kelas;

    public function mount($slugGuruMapel)
    {
        $this->slugGuruMapel = $slugGuruMapel;
        // Ambil mata pelajaran berdasarkan slug
        $this->guruMapel = GuruMataPelajaran::where('slug', $slugGuruMapel)->first();
        if (!$this->guruMapel) {
            abort(404);
        }
        $this->mataPelajaran = $this->guruMapel->mataPelajaran->nama;
        $this->sesiBelajar = SesiBelajar::where('id_guru_mata_pelajaran', $this->guruMapel->id)->get()->toArray();
        
        $this->kelas = $this->guruMapel->jadwalPelajaran->map(function ($item) {
            return [
                "id_kelas" => $item->id_kelas,
                "nama_kelas" => $item->kelas->nama,
            ];
        })->unique()->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('judul')
                ->label('Sesi Belajar')
                ->required()

        ];
    }
    public function save()
    {
        $this->validate([
            'judul' => 'required'
        ]);

        $guruMapel = GuruMataPelajaran::where('slug', $this->guruMapel->slug)->first();

        if (!empty($this->judul)) {
            SesiBelajar::create([
                'judul' => $this->judul,
                'id_guru_mata_pelajaran' => $guruMapel->id
            ]);
        }

        Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Sesi Belajar berhasil ditambah.')
            ->send();
        // Clear the form
        $this->dispatch('close-modal', id: 'tambah-sesi-modal');

        // Refresh the table
        $this->sesiBelajar = SesiBelajar::where('id_guru_mata_pelajaran', $this->guruMapel->id)->get()->toArray();
    }

    public function deleteSesiBelajar($idSesiBelajar)
    {
        $sesiBelajar = SesiBelajar::find($idSesiBelajar);

        if ($sesiBelajar) {
            $sesiBelajar->delete();

            Notification::make()
                ->success()
                ->title('Berhasil')
                ->body('Sesi Belajar berhasil dihapus.')
                ->send();

            // Refresh data sesi belajar
            $this->sesiBelajar = SesiBelajar::where('id_guru_mata_pelajaran', $this->guruMapel->id)->get()->toArray();
        } else {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Sesi Belajar tidak ditemukan.')
                ->send();
        }
    }

}
