<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Guru extends Authenticatable
{
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'name',
        'email',
        'password',
    ];

    // Kolom password dienkripsi$
    protected $casts = [
        'password' => 'hashed',
    ];

    public function mataPelajarans(): BelongsToMany{
        return $this->belongsToMany(MataPelajaran::class, 'guru_mata_pelajarans', 'id_guru', 'id_mata_pelajaran');
    }

    public function jadwalPelajarans(): HasManyThrough
    {
        return $this->hasManyThrough(JadwalPelajaran::class, GuruMataPelajaran::class, 'id_guru', 'id_guru_mata_pelajaran');
    }

    public function kuis()
    {
        return $this->hasMany(Kuis::class, 'id_guru', 'id');
    }



      public function sesiBelajars(): HasManyThrough
    {
        return $this->hasManyThrough(
            SesiBelajar::class,        // related (tujuan)
            GuruMataPelajaran::class,  // through  (perantara)
            'id_guru',                 // firstKey: FK di tabel through -> gurus.id
            'id_guru_mata_pelajaran',  // secondKey: FK di tabel related -> gmp.id
            'id',                      // localKey: PK di gurus   ✅ JANGAN 'id_kelas'
            'id'                       // secondLocalKey: PK di guru_mata_pelajarans
        );
    }
}
