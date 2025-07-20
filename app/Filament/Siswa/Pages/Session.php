<?php

namespace App\Filament\Siswa\Pages;

use App\Models\SesiBelajar;
use Filament\Pages\Page;
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
    protected static ?string $slug = 'my-courses/session/{slug}'; // Custom URL slug

    public function mount($slug)
    {
        $this->sesiBelajar = SesiBelajar::where('slug', $slug)->first();
    
        $this->materi = $this->sesiBelajar->materi;
       
        $this->tugas = $this->sesiBelajar->tugas;
        $this->kuis = $this->sesiBelajar->kuis;
        $this->fileMateri = $this->sesiBelajar->fileMateris;
        // ambil sesion
        $this->slugMapel = session('slugMapel'); // Ambil slugMapel dari session
        session()->put('slugSession', $slug);
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
    public function startQuiz($slugQuiz){
        session()->put('slugQuiz', $slugQuiz);
        return redirect()->route('filament.siswa.pages.show-quiz');
    }

    public function lihatHasil($slugQuiz){
        return redirect()->route('filament.siswa.pages.quiz-result.{slugQuiz}', ['slugQuiz' => $slugQuiz]);
    }

}
