<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
     protected $fillable = [
        'id_hasil_kuis',
        'id_pertanyaan',
        'id_jawaban',
    ];

    protected $table = 'jawaban_siswa';

    public function hasilKuis()
    {
        return $this->belongsTo(HasilKuis::class, 'id_hasil_kuis');
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'id_pertanyaan');
    }

    public function jawaban()
    {
        return $this->belongsTo(Jawaban::class, 'id_jawaban');
    }
}
