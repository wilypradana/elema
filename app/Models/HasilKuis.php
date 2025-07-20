<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasilKuis extends Model
{
     protected $fillable = [
        'id_kuis',
        'id_siswa',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'skor',
        'questions',
    ];

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class, 'id_kuis', 'id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    public function jawabanSiswa(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class, 'id_hasil_kuis');
    }

     // Accessor for questions
     public function getQuestionsAttribute($value)
     {
         return json_decode($value, true);
     }
 
     // Mutator for questions
     public function setQuestionsAttribute($value)
     {
         $this->attributes['questions'] = json_encode($value);
     }

}
