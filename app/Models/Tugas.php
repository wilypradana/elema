<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tugas extends Model
{

    protected $fillable = [
        'judul', 'deskripsi', 'deadline', 'id_sesi_belajar',
    ];
    public function sesiBelajars(): BelongsTo{
        return $this->belongsTo(SesiBelajar::class, "id_sesi_belajar");
    }

    public function siswas(): BelongsToMany{
        return $this->belongsToMany(Siswa::class, 'pengumpulan_tugas', 'id_tugas', 'id_siswa');
    }

     // Relasi ke pengumpulan tugas
     public function pengumpulanTugas(){
        return $this->hasMany(PengumpulanTugas::class, 'id_tugas');
    }
}
