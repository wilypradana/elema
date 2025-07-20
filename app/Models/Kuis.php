<?php

namespace App\Models;

use App\Models\SesiBelajar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kuis extends Model
{

     protected $fillable = [
        'judul',
        'deskripsi',
        'aktif',
        'durasi',
        'waktu_mulai',
        'waktu_selesai',
        'acak_soal',
        'nilai_minimal',
        'slug',
        'id_guru',
    ];
    
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Mengisi slug secara otomatis
            $model->slug = Str::random(10);
        });
    }
    public function siswa(): BelongsToMany{
        return $this->belongsToMany(Siswa::class, 'hasil_kuis', 'id_kuis', 'id_siswa');
    }
    public function sesiBelajars(): BelongsToMany{
        return $this->belongsToMany(SesiBelajar::class,"kuis_sesi_belajar","id_kuis","id_sesi_belajar");
    }

    public function pertanyaans(): HasMany{
        return $this->hasMany(Pertanyaan::class, 'id_kuis', 'id');
    }

    public function hasilKuis(): HasMany{
        return $this->HasMany(HasilKuis::class, 'id_kuis', 'id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id');
    }
}
