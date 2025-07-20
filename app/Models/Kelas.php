<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

     protected $fillable = [
        'kode',
        'nama',
        'id_jurusan',
        'id_angkatan',
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id');
    }

    public function angkatan(): BelongsTo
    {
        return $this->belongsTo(Angkatan::class, 'id_angkatan', 'id');
    }

    public function siswas(): HasMany{
        return $this->hasMany(Siswa::class, 'id_kelas', 'id');
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_kelas', 'id');
    }


}
