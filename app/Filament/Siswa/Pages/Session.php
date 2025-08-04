<?php
namespace App\Filament\Siswa\Pages;

use App\Models\SesiBelajar;
use App\Models\Siswa;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Session extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.siswa.pages.session';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'sesi belajar';
    
    public $materi;
    public $tugas;
    public $fileMateri;
    public $kuis;
    public $sesiBelajar;
    public $slugMapel;
    public $siswa;
    
    protected static ?string $slug = 'my-courses/session/{slug}'; // Custom URL slug

    public function mount($slug)
    {
        $this->siswa = Auth::user()->siswa ?? Siswa::where('id', Auth::id())->first();
        
        if (!$this->siswa) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        // Query SesiBelajar dengan filter berdasarkan kelas siswa
        $this->sesiBelajar = SesiBelajar::where('slug', $slug)
            ->where(function($query) {
                $query->where('id_kelas', $this->siswa->id_kelas)
                      ->orWhereNull('id_kelas'); // Untuk backward compatibility dengan data lama
            })
            ->first();

        // Jika sesi belajar tidak ditemukan atau tidak sesuai dengan kelas siswa
        if (!$this->sesiBelajar) {
            abort(404, 'Sesi belajar tidak ditemukan atau tidak tersedia untuk kelas Anda');
        }

        // Validasi tambahan: pastikan siswa memiliki akses ke mata pelajaran ini
        $hasAccess = $this->validateStudentAccess();
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke sesi belajar ini');
        }

        // Load relasi data seperti sebelumnya
        $this->materi = $this->sesiBelajar->materi;
        $this->tugas = $this->sesiBelajar->tugas;
        $this->kuis = $this->sesiBelajar->kuis;
        $this->fileMateri = $this->sesiBelajar->fileMateris;
        
        // Ambil session
        $this->slugMapel = session('slugMapel');
        session()->put('slugSession', $slug);
    }

    /**
     * Validasi apakah siswa memiliki akses ke mata pelajaran ini
     */
    private function validateStudentAccess(): bool
    {
        // Cek apakah siswa memiliki jadwal pelajaran yang sesuai dengan guru mata pelajaran dari sesi ini
        $guruMataPelajaran = $this->sesiBelajar->guruMataPelajaran;
        
        // Cek melalui jadwal pelajaran siswa
        $hasAccess = $guruMataPelajaran->jadwalPelajaran()
            ->where('id_kelas', $this->siswa->id_kelas)
            ->exists();

        return $hasAccess;
    }

    /**
     * Ambil semua sesi belajar yang tersedia untuk siswa berdasarkan kelasnya
     */
    public function getAvailableSessionsForStudent()
    {
        if (!$this->siswa) {
            return collect();
        }

        return SesiBelajar::where('id_kelas', $this->siswa->id_kelas)
            ->orWhereNull('id_kelas') // Untuk backward compatibility
            ->with(['guruMataPelajaran.mataPelajaran', 'kelas'])
            ->get();
    }

    public function downloadFile($path): BinaryFileResponse
    {
        // Jika file tersimpan di storage Laravel, gunakan storage_path atau public_path
        $fullPath = storage_path('app/public/' . $path);
        
        // Pastikan file ada
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan');
        }
        
        // Mengembalikan response download
        return response()->download($fullPath);
    }

    public function kumpulkanTugas($idTugas)
    {
        return redirect()->route('filament.siswa.pages.submission.{idTugas}.session.{slugSesi}', [
            'idTugas' => $idTugas,
            'slugSesi' => $this->sesiBelajar->slug,
        ]);
    }

    public function lanjutkanKuis($slug)
    {
        $kuis = $this->sesiBelajar->kuis()->where('slug', $slug)->first();
        
        if (now('Asia/Jakarta')->greaterThan($kuis->waktu_selesai)) {
            return redirect()->route('filament.siswa.pages.quiz-result.{slugQuiz}', ['slugQuiz' => $slug])
                ->with('message', 'Waktu kuis sudah habis. Silakan lihat hasil kuis.');
        }
    
        // Logic to continue the quiz
        return redirect()->route('filament.siswa.pages.show-quiz', ['slugQuiz' => $slug]);
    }

    public function startQuiz($slugQuiz)
    {
        session()->put('slugQuiz', $slugQuiz);
        return redirect()->route('filament.siswa.pages.show-quiz');
    }

    public function lihatHasil($slugQuiz)
    {
        return redirect()->route('filament.siswa.pages.quiz-result.{slugQuiz}', ['slugQuiz' => $slugQuiz]);
    }
}