<?php

namespace App\Filament\Siswa\Pages;

use App\Models\HasilKuis;
use App\Models\JawabanSiswa;
use App\Models\Kuis;
use App\Models\Jawaban;
use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Actions\Action;

class ShowQuiz extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.siswa.pages.show-quiz';
    protected static bool $shouldRegisterNavigation = false;

    public $slugQuiz;
    public $kuis;
    public $soals;
    public $jumlahSoal;
    public $jawaban = [];
    public $hasilKuis;
    public $durasi;
    public $skor = 0;

    public function mount()
    {
        $this->slugQuiz = session('slugQuiz');
        $this->kuis = Kuis::with('pertanyaans.jawabans')->where('slug', $this->slugQuiz)->first();

        // Load or create HasilKuis record
        $this->hasilKuis = HasilKuis::firstOrCreate([
            'id_kuis' => $this->kuis->id,
            'id_siswa' => auth()->id(),
        ], [
            'waktu_mulai' => now("Asia/Jakarta"),
            'status' => 'in_progress',
        ]);

        // Load or shuffle questions
        if ($this->hasilKuis->questions) {
            $this->soals = collect($this->hasilKuis->questions);
        } else {
            $this->soals = $this->kuis->acak_soal ? $this->kuis->pertanyaans->shuffle() : $this->kuis->pertanyaans;
            $this->hasilKuis->questions = $this->soals;
            $this->hasilKuis->save();
        }

        $this->jumlahSoal = $this->soals->count();
        $this->jawaban = array_fill(0, $this->jumlahSoal, null);
        $this->durasi = $this->kuis->durasi;
        // Load existing answers if any
        $existingAnswers = JawabanSiswa::where('id_hasil_kuis', $this->hasilKuis->id)->get();
        foreach ($existingAnswers as $answer) {
            $index = $this->soals->search(function ($soal) use ($answer) {
                return $soal['id'] === $answer['id_pertanyaan'];
            });
            if ($index !== false) {
                $this->jawaban[$index] = $answer['id_jawaban'];
            }
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make(
                $this->soals->map(function ($soal, $index) {
                    return Forms\Components\Wizard\Step::make("soal ".$index+1)
                        ->schema([
                                    Forms\Components\Placeholder::make('pertanyaan')
                                        ->label('')
                                        ->content(new \Illuminate\Support\HtmlString($soal['pertanyaan'])),
                                    Forms\Components\Radio::make('jawaban.' . $index)
                                        ->label('Pilih jawaban:')
                                        ->options(collect($soal['jawabans'])->pluck('jawaban', 'id')->toArray())
                                        ->required()
                                        ->afterStateUpdated(function ($state) use ($index, $soal) {
                                            $this->simpanJawaban($index, $soal['id'], $state);
                                        }),
                        ])->icon('heroicon-o-document');
                })->toArray()
            )
                ->nextAction(function (Action $action) {
                    $action->label('Next')
                        ->action(function () {
                            // Check if the current time is past the quiz end time
                            if (Carbon::now('Asia/Jakarta')->greaterThan($this->kuis->waktu_selesai)) {
                                session()->flash('error', 'Waktu pengerjaan kuis telah berakhir.');
                                $this->hasilKuis->update(['status' => 'expired', 'waktu_selesai' => now("Asia/Jakarta")]);
                                return redirect()->route('filament.siswa.pages.quiz-result.{slugQuiz}', ['slugQuiz' => $this->slugQuiz]);
                            }

                            $currentStep = $this->form->getState()['currentStep'];
                            $index = $currentStep - 1;
                            $pertanyaanId = $this->soals[$index]['id'];
                            $jawabanId = $this->jawaban[$index];

                            $this->simpanJawaban($index, $pertanyaanId, $jawabanId);

                            $this->form->statePath('currentStep')->increment();
                        });
                })
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
        <x-filament::button
            wire:click="selesaiKuis"
            type="submit"
            size="sm"
            label="Selesai"
        >
           Selesai
        </x-filament::button>
    BLADE)))
        ];
    }

    public function simpanJawaban($index, $pertanyaanId, $jawabanId)
    {
        // Check if the current time is past the quiz end time
        if (Carbon::now('Asia/Jakarta')->greaterThan($this->kuis->waktu_selesai)) {
            session()->flash('error', 'Waktu pengerjaan kuis telah berakhir.');
            $this->hasilKuis->update([
                'status' => 'expired',
                'waktu_selesai' => now("Asia/Jakarta"),]);
            return redirect()->route('filament.siswa.pages.quiz-result.{slugQuiz}', ['slugQuiz' => $this->slugQuiz]);
        }

        DB::transaction(function () use ($index, $pertanyaanId, $jawabanId) {
            // Only save the answer if the student has selected an answer
            if ($jawabanId !== null) {
                JawabanSiswa::updateOrCreate([
                    'id_hasil_kuis' => $this->hasilKuis->id,
                    'id_pertanyaan' => $pertanyaanId,
                ], [
                    'id_jawaban' => $jawabanId,
                ]);
    
                $jawabanBenar = Jawaban::where('id', $jawabanId)
                    ->where('jawaban_benar', true)
                    ->exists();
                if ($jawabanBenar) {
                    $this->hasilKuis->increment('skor', $this->soals[$index]['bobot']);
                }
            }
        });
    }

    public function selesaiKuis()
    {
        // Check if the current time is past the quiz end time
        if (Carbon::now('Asia/Jakarta')->greaterThan($this->kuis->waktu_selesai)) {
            session()->flash('error', 'Waktu pengerjaan kuis telah berakhir.');
            $this->hasilKuis->update(['status' => 'expired']);
            return redirect()->route('filament.siswa.pages.quiz-result.{slugQuiz}', ['slugQuiz' => $this->slugQuiz]);
        }

        $this->hasilKuis->update([
            'waktu_selesai' => now("Asia/Jakarta"),
            'status' => 'completed',
        ]);

        session()->flash('status', 'Kuis selesai! Nilai Anda: ' . $this->hasilKuis->skor);
        return redirect()->route('filament.siswa.pages.quiz-result.{slugQuiz}', ['slugQuiz' => $this->slugQuiz]);
    }
}
