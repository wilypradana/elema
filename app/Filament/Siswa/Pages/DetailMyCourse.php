<?php

namespace App\Filament\Siswa\Pages;

use Filament\Pages\Page;
use App\Models\SesiBelajar;
use App\Models\GuruMataPelajaran;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;

class DetailMyCourse extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.siswa.pages.detail-my-course';
    protected static ?string $slug = 'my-courses/{slugMapel}'; // Custom URL slug
    protected static ?string $title = 'daftar sesi';

    public $guruMapel;
    public $mataPelajaran;
    public $sesiBelajars = [];
    public $slugMapel;
public function mount($slugMapel)
{
    $siswa = Auth::user()->siswa ?? Siswa::where('id', Auth::id())->first();

    if (!$siswa) {
        abort(403, 'Data siswa tidak ditemukan.');
    }

    $this->guruMapel = GuruMataPelajaran::where('slug', $slugMapel)->first();

    $this->sesiBelajars = $this->guruMapel->sesiBelajar()
        ->where(function ($query) use ($siswa) {
            $query->where('id_kelas', $siswa->id_kelas)
                  ->orWhereNull('id_kelas');
        })
        ->get();

    session()->put('slugMapel', $slugMapel);
}


    public function sesiBelajar($slug)
    {   
        return redirect()->route('filament.siswa.pages.my-courses.session.{slug}', ['slug' => $slug]);
    }
}
