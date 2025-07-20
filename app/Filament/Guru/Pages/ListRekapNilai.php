<?php
namespace App\Filament\Guru\Pages;

use App\Exports\SiswaNilaiExport;
use App\Models\GuruMataPelajaran;
use App\Models\HasilKuis;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;

class ListRekapNilai extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.guru.pages.list-rekap-nilai';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    protected static ?string $slug = 'list-rekap-nilai/{slug}';

    public $guruMapel;
    public $idKelas;
    public $kelas;
    public $siswaNilai = [];

    public function mount($slug)
    {
        // Ambil data GuruMataPelajaran dan Kelas
        $this->guruMapel = $this->getGuruMataPelajaran($slug);
        $this->kelas = $this->getKelas();

        // Ambil daftar siswa dan proses nilainya
        $siswaList = $this->getSiswaList();
        $this->siswaNilai = $this->processNilaiSiswa($siswaList);
    }

    protected function getGuruMataPelajaran($slug)
    {
        // Mengambil data GuruMataPelajaran beserta relasi ke sesiBelajar dan kuis.hasilKuis
        return GuruMataPelajaran::query()
            ->where('slug', $slug)
            ->with([
                'mataPelajaran',
                'sesiBelajar.kuis.hasilKuis',
                'sesiBelajar.tugas',
            ])
            ->firstOrFail();
    }

    protected function getKelas()
    {
        // Ambil ID Kelas dari query string dan cek apakah kelas ditemukan
        $this->idKelas = request()->query('kelas');
        return Kelas::findOrFail($this->idKelas);
    }

    protected function getSiswaList()
    {
        // Ambil daftar siswa beserta relasi pengumpulan tugas dan hasil kuis
        return Siswa::where('id_kelas', request()->query('kelas'))
            ->get();
    }

    protected function processNilaiSiswa($siswaList)
    {
        $listNilaiSiswa = [];

        foreach ($siswaList as $siswa) {
            $nilaiSiswa = $this->getNilaiSiswa($siswa);
            $listNilaiSiswa[] = $nilaiSiswa;
        }

        return $listNilaiSiswa;
    }

    protected function getNilaiSiswa($siswa)
    {
        // Inisialisasi nilai siswa
        $nilaiSiswa = [
            'nama_siswa' => $siswa->name,
            'mata_pelajaran' => $this->guruMapel->mataPelajaran->nama,
            'kelas' => $this->kelas->nama,
            'nilai_sesi' => [],
        ];

        // Proses nilai per sesi belajar
        foreach ($this->guruMapel->sesiBelajar as $sesi) {
            $nilaiSiswa['nilai_sesi'][] = $this->getNilaiPerSesi($siswa, $sesi);
        }

        return $nilaiSiswa;
    }

    protected function getNilaiPerSesi($siswa, $sesi)
    {
        // Ambil nilai tugas
        $nilaiTugasSiswa = $this->getNilaiTugas($siswa, $sesi);

        // Ambil nilai kuis
        $nilaiKuis = $this->getNilaiKuis($siswa, $sesi);

        // Return nilai sesi dalam bentuk array
        return [
            'sesi' => $sesi->judul,
            'nilai_tugas' => $nilaiTugasSiswa,
            'nilai_kuis' => $nilaiKuis,
        ];
    }

    protected function getNilaiTugas($siswa, $sesi)
    {
        // Ambil semua tugas dari sesi ini dengan eager loading pengumpulan tugas untuk siswa tertentu
        $tugasList = $sesi->tugas()->with([
            'pengumpulanTugas' => function ($query) use ($siswa) {
                $query->where('id_siswa', $siswa->id);
            }
        ])->get();

        $totalNilaiTugasSiswa = 0; // Default nilai 0

        // Cek apakah ada tugas dalam sesi ini
        if ($tugasList->isNotEmpty()) {
            foreach ($tugasList as $tugas) {
                // Ambil data pengumpulan tugas pertama yang terkait dengan siswa ini
                $pengumpulanTugas = $tugas->pengumpulanTugas->first();

                // Jika ada pengumpulan tugas dan nilainya lebih dari 0, tambahkan ke total nilai
                if ($pengumpulanTugas && $pengumpulanTugas->nilai > 0) {
                    // Hanya tambahkan nilai pengumpulan pertama yang ditemukan
                    $totalNilaiTugasSiswa = $pengumpulanTugas->nilai;
                    break; // Keluar dari loop setelah menemukan nilai pertama
                }
            }

            // Jika ada nilai tugas yang valid, return totalnya
            return $totalNilaiTugasSiswa > 0 ? $totalNilaiTugasSiswa : 0;
        }

        return "tidak tersedia"; // Jika tidak ada tugas atau nilai tugas 0
    }




    protected function getNilaiKuis($siswa, $sesi)
    {
        // Cek apakah sesi memiliki kuis
        if ($sesi->kuis->isNotEmpty()) {
            // Inisialisasi variabel untuk menyimpan nilai kuis siswa
            $nilaiKuisSiswa = 0; // Default jika tidak ada nilai

            // Loop melalui semua kuis dalam sesi
            foreach ($sesi->kuis as $kuis) {
                // Ambil hasil kuis pertama untuk siswa tertentu
                $hasilKuis = $kuis->hasilKuis->where('id_siswa', $siswa->id)->first();

                // Jika ditemukan hasil kuis untuk siswa, simpan nilainya
                if ($hasilKuis) {
                    $nilaiKuisSiswa = $hasilKuis->skor;
                    break; // Keluar dari loop setelah menemukan hasil kuis yang valid
                }
            }

            // Kembalikan nilai kuis yang ditemukan, jika tidak return 0
            return $nilaiKuisSiswa > 0 ? $nilaiKuisSiswa : 0;
        }

        return "tidak tersedia"; // Jika tidak ada kuis dalam sesi ini
    }



    public function exportNilaiSiswa()
    {
        // Ambil data nilai siswa, misalnya dari service atau query
        $siswaNilai = $this->getSiswaNilai(); // Ganti dengan fungsi yang sesuai

        // Export file dalam format Excel
        return Excel::download(new SiswaNilaiExport($siswaNilai), 'nilai-siswa.xlsx');
    }

    // Implementasi method untuk mengambil data siswa dan nilai
    protected function getSiswaNilai()
    {
        return $this->siswaNilai;
    }
}
