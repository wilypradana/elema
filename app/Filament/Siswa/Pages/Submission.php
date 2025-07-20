<?php

namespace App\Filament\Siswa\Pages;

use App\Models\FilePengumpulanTugas;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Submission extends Page
{
    use InteractsWithForms;
    use InteractsWithFormActions;
    protected static ?string $model = PengumpulanTugas::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.siswa.pages.submission';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'submission/{idTugas}/session/{slugSesi}';

    public $idTugas;
    public $slugSesi;
    public $tugas;
    public $siswa;
    public ?array $file = [];
    public $pengumpulanTugas;
    public $filePengumpulanTugas;
    public $slugSession;
    public $slugMapel;
    public function getTugas($id): self
    {
        $this->tugas = Tugas::find($id);
        return $this;
    }

    public function getPengumpulanTugas()
    {
        $this->pengumpulanTugas = PengumpulanTugas::where('id_tugas', $this->idTugas)->where('id_siswa', $this->siswa->id)->first();
    }
    public function getSiswa()
    {
        $this->siswa = Auth::user();
    }
    public function mount($idTugas, $slugSesi)
    {
        $this->idTugas = $idTugas;
        $this->slugSesi = $slugSesi;
        $this->getTugas($idTugas);
        $this->getSiswa();
        $this->getPengumpulanTugas();
        // ambil session
        $this->slugSession = session('slugSession');// Ambil slugSession dari session
    }

    public function getFormSchema(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    FileUpload::make('file')
                        ->required()
                        ->multiple()
                        ->maxFiles(2048)
                        ->helperText('Ukuran maksimal file 2MB per file.')
                ]),
        ];
    }

    public function save()
    {
        $this->validate([
            'file' => 'required',
        ]);

        $filePaths = []; // Array untuk menyimpan path file yang berhasil disimpan

        // Iterasi melalui array file dan simpan satu per satu
        foreach ($this->file as $file) {
            if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // Pastikan file adalah objek TemporaryUploadedFile sebelum kita menyimpannya
                // Membuat nama acak untuk file
                $randomName = Str::uuid() . '.' . $file->getClientOriginalExtension();

                // Simpan file di disk 'public' dengan nama acak
                $filePath = $file->storeAs('filepengumpulantugas', $randomName, 'public');
                // Simpan path dan nama asli file ke dalam array
                $filePaths[] = [
                    'path' => $filePath,
                    'name' => $file->getClientOriginalName(),
                ];

                // Hapus file sementara setelah upload selesai
                if (file_exists($file->getRealPath())) {
                    unlink($file->getRealPath());
                }
            }
        }

        $pengumpulanTugas = PengumpulanTugas::create([
            'id_tugas' => $this->tugas->id,
            'id_siswa' => $this->siswa->id,
            'created_at' => Carbon::now('Asia/Jakarta'),
            'updated_at' => Carbon::now('Asia/Jakarta')
        ]);

        // Simpan data ke database untuk setiap file yang di-upload
        foreach ($filePaths as $fileData) {
            FilePengumpulanTugas::create([
                'pengumpulan_tugas_id' => $pengumpulanTugas->id,
                'file' => $fileData['path'],  // Simpan path file (nama acak)
                'nama_file' => $fileData['name'], // Simpan nama asli file
            ]);
        }

        // Refresh table atau halaman
        return redirect()->route('filament.siswa.pages.submission.{idTugas}.session.{slugSesi}', [
            'idTugas' => $this->tugas->id,
            'slugSesi' => $this->slugSesi,
        ]);
    }

    public function edit($slugSubmission)
    {
        return redirect()->route('filament.siswa.pages.submission.{idTugas}.session.{slugSesi}.edit', [
            'idTugas' => $this->tugas->id,
            'slugSesi' => $this->slugSesi,
            'slugSubmission' => $slugSubmission
        ]);
    }

    public $confirmingDelete = false;

    public function confirmDelete()
    {
        $this->confirmingDelete = true;
    }

    public function deleteSubmission()
    {
        // Pastikan pengumpulan tugas ada
        if (!$this->pengumpulanTugas) {
            return;
        }

        // Menghapus file yang terkait
        $filePengumpulanTugas = FilePengumpulanTugas::where('pengumpulan_tugas_id', $this->pengumpulanTugas->id)->get();

        foreach ($filePengumpulanTugas as $file) {
            // Hapus file dari storage
            if (Storage::disk('public')->exists($file->file)) {
                Storage::disk('public')->delete($file->file);
            }

            // Hapus dari database
            $file->delete();
        }

        // Hapus pengumpulan tugas dari database
        $this->pengumpulanTugas->delete();


        // Refresh halaman
        return redirect()->route('filament.siswa.pages.submission.{idTugas}.session.{slugSesi}', [
            'idTugas' => $this->tugas->id,
            'slugSesi' => $this->slugSesi,
        ]);
    }
    public function backToSession()
    {
        return redirect()->route('filament.siswa.pages.my-courses.session.{slug}', [
            'slug' => $this->slugSession,
        ]);
    }
}
