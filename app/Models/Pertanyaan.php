<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pertanyaan extends Model
{
    protected $fillable = [
        'id_kuis',
        'pertanyaan',
        'bobot',
    ];
    
    public function kuis(): BelongsTo{
        return $this->belongsTo(Kuis::class, 'id_kuis', 'id');
    }

    public function jawabans() : HasMany{
        return $this->hasMany(Jawaban::class, 'id_pertanyaan', 'id');
    }
}
