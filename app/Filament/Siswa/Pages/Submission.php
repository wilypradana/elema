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
            Grid::make(1)->schema([
            FileUpload::make('file')
            ->required()
            ->multiple()
            ->maxFiles(10)
            ->maxSize(10240)
            ->acceptedFileTypes([
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/pdf',
            ])
            ->rules(['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf'])
            ->disk('public')
            ->directory('filemateri')
            ->visibility('public')
            ->helperText('Ukuran maksimal 10MB per file (JPG/PNG/GIF/WebP/PDF).')

    ]),
];

    }

  public function save()
{
    try {
        // Log awal
        \Log::info('DEBUG: Mulai simpan submission', [
            'id_tugas' => $this->idTugas,
            'id_siswa' => $this->siswa->id ?? null,
            'jumlah_file' => count($this->file ?? []),
            'tipe_file' => gettype($this->file),
        ]);

        $this->validate([
            'file' => 'required',
        ]);

        $filePaths = [];

        foreach ($this->file as $file) {
            \Log::info('DEBUG: Iterasi file', [
                'class' => get_class($file),
                'original_name' => method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : null,
                'size' => method_exists($file, 'getSize') ? $file->getSize() : null,
                'mime' => method_exists($file, 'getMimeType') ? $file->getMimeType() : null,
            ]);

            if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $randomName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('filepengumpulantugas', $randomName, 'public');

                \Log::info('DEBUG: File tersimpan', [
                    'filePath' => $filePath,
                ]);

                $filePaths[] = [
                    'path' => $filePath,
                    'name' => $file->getClientOriginalName(),
                ];
            } else {
                \Log::warning('DEBUG: File bukan TemporaryUploadedFile', [
                    'type' => get_class($file),
                ]);
            }
        }

        $pengumpulanTugas = PengumpulanTugas::create([
            'id_tugas' => $this->tugas->id,
            'id_siswa' => $this->siswa->id,
            'created_at' => Carbon::now('Asia/Jakarta'),
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ]);

        \Log::info('DEBUG: PengumpulanTugas dibuat', [
            'id' => $pengumpulanTugas->id,
        ]);

        foreach ($filePaths as $fileData) {
            FilePengumpulanTugas::create([
                'pengumpulan_tugas_id' => $pengumpulanTugas->id,
                'file' => $fileData['path'],
                'nama_file' => $fileData['name'],
            ]);

            \Log::info('DEBUG: FilePengumpulanTugas dibuat', [
                'nama_file' => $fileData['name'],
                'path' => $fileData['path'],
            ]);
        }

        \Log::info('DEBUG: Selesai simpan semua data');

      
    } catch (\Throwable $e) {
        \Log::error('ERROR: Gagal simpan submission', [
            'msg' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        throw $e; 
    }
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
