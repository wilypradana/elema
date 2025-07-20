<?php

namespace App\Filament\Siswa\Widgets;

use App\Models\GuruMataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\TahunPelajaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $siswa = Auth::guard('student')->user();

        $tahunPelajaranAktif = TahunPelajaran::where('aktif', true)->first();

        // Tangani jika tahun pelajaran belum diatur
        if (!$tahunPelajaranAktif) {
            \Filament\Notifications\Notification::make()
                ->title('Tahun Pelajaran Belum Diatur')
                ->body('Admin akan mengatur jadwal pelajaran segera.')
                ->danger()
                ->send();

            return [];
        }

        // Buat cache key unik berdasarkan ID siswa
        $cacheKey = 'jadwal_mapel_siswa_' . $siswa->id;

        // Gunakan cache selama 30 menit
        $mataPelajaran = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($siswa, $tahunPelajaranAktif) {
            $jadwalByMataPelajaran = JadwalPelajaran::where('id_tahun_pelajaran', $tahunPelajaranAktif->id)
                ->whereHas('guruMataPelajaran', function ($query) use ($siswa) {
                    $query->where('id_kelas', $siswa->id_kelas);
                })
                ->with([
                    'guruMataPelajaran' => function ($query) {
                        $query->select('id', 'id_guru', 'id_mata_pelajaran', 'slug')
                            ->with([
                                'mataPelajaran:id,nama',
                                'guru:id,name',
                            ]);
                    },
                ])
                ->get()
                ->groupBy('guruMataPelajaran.id');

            return $jadwalByMataPelajaran->map(function ($jadwals) {
                $guruMapel = $jadwals->first()->guruMataPelajaran;
                $hari = $jadwals->first()->hari;

                return [
                    'mata_pelajaran' => $guruMapel->mataPelajaran->nama,
                    'guru' => $guruMapel->guru->name,
                    'hari' => $hari,
                    'slug_mapel' => $guruMapel->slug,
                ];
            });
        });

        // Ubah data yang sudah di-cache menjadi array Stat
        return $mataPelajaran->map(function ($mapel) {
            return Stat::make(
                'Hari: ' . $mapel['hari'],
                new HtmlString('<span class="text-lg font-bold">' . e($mapel['mata_pelajaran']) . '</span>')
            )
                ->description($mapel['guru'])
                ->icon('heroicon-o-book-open')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => 'myCourse(\'' . $mapel['slug_mapel'] . '\')',
                    'wire:loading.attr' => 'enabled',
                ]);
        })->toArray();
    }

    // Method yang akan dipanggil saat Stat diklik
    public function myCourse($slugMapel)
    {
        return redirect()->route('filament.siswa.pages.my-courses.{slugMapel}', ['slugMapel' => $slugMapel]);
    }
}
