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
}
