<?php

namespace App\Filament\Guru\Widgets;

use App\Models\GuruMataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\TahunPelajaran;
use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MataPelajaranStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $guru = Auth::user();

        try {
            // Ambil tahun pelajaran aktif
            $tahunPelajaranAktif = TahunPelajaran::where('aktif', true)->first();

            // Kalau tidak ada tahun pelajaran aktif, tampilkan notifikasi lalu hentikan proses
            if (!$tahunPelajaranAktif) {
                Notification::make()
                    ->title('Tahun Pelajaran Belum Diatur')
                    ->body('Silakan hubungi admin untuk mengatur jadwal pelajaran.')
                    ->danger()
                    ->send();

                return [];
            }

            // Cek apakah data sudah ada di cache
            $cacheKey = 'jadwal_mapel_guru_' . $guru->id . '_tahun_' . $tahunPelajaranAktif->id;
            $jadwalByMataPelajaran = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($guru, $tahunPelajaranAktif) {
                return JadwalPelajaran::where('id_tahun_pelajaran', $tahunPelajaranAktif->id)
                    ->whereHas('guruMataPelajaran', function ($query) use ($guru) {
                        $query->where('id_guru', $guru->id);
                    })
                    ->with([
                        'guruMataPelajaran' => function ($query) {
                            $query->select('id', 'id_guru', 'id_mata_pelajaran', 'slug')
                                ->with([
                                    'mataPelajaran:id,nama',
                                ]);
                        },
                        'kelas:id,nama'
                    ])
                    ->get()
                    ->groupBy('guruMataPelajaran.mataPelajaran.id');
            });

            // Buat array stats
            $stats = $jadwalByMataPelajaran->map(function ($jadwals) {
                $mataPelajaran = $jadwals->first()->guruMataPelajaran->mataPelajaran;
                // Ambil semua nama kelas
                $kelasNames = $jadwals->pluck('kelas.nama')->toArray();
                $slugMapel = $jadwals->first()->guruMataPelajaran->slug;
                // Gabungkan nama kelas menjadi satu string
                $kelasString = implode(', ', $kelasNames);

                return Stat::make(
                    $jadwals->count() . ' Jadwal',
                    $mataPelajaran->nama
                )
                    ->description('Kelas: ' . $kelasString)
                    ->icon('heroicon-o-book-open')
                    ->color('primary')
                    ->extraAttributes([
                        'class' => 'cursor-pointer',
                        'wire:click' => 'kelolaJadwal(\'' . $slugMapel . '\')',
                        'wire:loading.attr' => 'enabled',
                    ]);
            })->toArray();

            return $stats;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return [];
        }
    }


    public function kelolaJadwal($slugGuruMapel)
    {
        return redirect()->route('filament.guru.pages.mata-pelajaran.{slugGuruMapel}', ['slugGuruMapel' => $slugGuruMapel]);
    }
}