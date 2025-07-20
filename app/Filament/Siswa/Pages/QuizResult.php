<?php

namespace App\Filament\Siswa\Pages;

use App\Models\HasilKuis;
use App\Models\Kuis;
use Filament\Pages\Page;

class QuizResult extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.siswa.pages.quiz-result';

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    protected static ?string $slug = 'quiz-result/{slugQuiz}'; // Custom URL slug
    public $hasilKuis;
    public $skor;
    public $jawabanSiswa;

    public function mount($slugQuiz)
    {
        $kuis = Kuis::where('slug', $slugQuiz)->first();

        if ($kuis) {
            // Ambil hasil kuis berdasarkan kuis dan siswa yang sedang login
            $this->hasilKuis = HasilKuis::with('jawabanSiswa.pertanyaan', 'jawabanSiswa.jawaban')
                ->where('id_kuis', $kuis->id)
                ->where('id_siswa', auth()->id())
                ->first();

            if ($this->hasilKuis) {
                // Ambil skor dan jawaban siswa
                $this->skor = $this->hasilKuis->skor;
                $this->jawabanSiswa = $this->hasilKuis->jawabanSiswa; // Asumsikan relasi antara HasilKuis dan JawabanSiswa
            }
        }
    }

    public function backToSession()
    {
        $slugSession = session('slugSession');
        return redirect()->route('filament.siswa.pages.my-courses.session.{slug}', ['slug' => $slugSession]);
    }


    public function getResults()
    {
        return [
            'skor' => $this->skor,
            'jawabanSiswa' => $this->jawabanSiswa,
        ];
    }
}
