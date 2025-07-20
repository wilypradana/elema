<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MataPelajaran extends Model
{
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

     protected $fillable = [
        'nama',
        'kode',
    ];

    public function gurus(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'guru_mata_pelajarans', 'id_mata_pelajaran', 'id_guru');
    }

    public function jadwalPelajarans()
    {
        return $this->hasManyThrough(JadwalPelajaran::class, GuruMataPelajaran::class, 'id_mata_pelajaran', 'id_guru_mata_pelajaran');
    }       
}
