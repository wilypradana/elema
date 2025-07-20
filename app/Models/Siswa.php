<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Siswa extends Authenticatable
{
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'nis', 'name', 'email', 'password', 'jenis_kelamin', 'id_kelas',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    
    public function  kelas(): BelongsTo{
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }

    public function  kela(): BelongsTo{
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }

    public function tugas(): BelongsToMany{
        return $this->belongsToMany(Tugas::class, 'pengumpulan_tugas', 'id_siswa', 'id_tugas');
    }
    

    public function kuis(): BelongsToMany{
        return $this->belongsToMany(Kuis::class, 'hasil_kuis', 'id_siswa', 'id_kuis');
    }
}
