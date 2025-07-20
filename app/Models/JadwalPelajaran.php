<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPelajaran extends Model
{
    protected $primaryKey = 'id'; // Kolom 'id' sebagai primary key secara default
    public $incrementing = true; // Menggunakan auto-increment untuk kolom 'id'
    public $timestamps = false;

    protected $fillable = [
        'id_guru_mata_pelajaran',
        'id_kelas',
        'id_tahun_pelajaran',
        'hari',
    ];
    
    /**
     * Relationship to Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }

    /**
     * Relationship to GuruMataPelajaran
     */
    public function guruMataPelajaran(): BelongsTo
    {
        return $this->belongsTo(GuruMataPelajaran::class, 'id_guru_mata_pelajaran', 'id');
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'id_tahun_pelajaran', 'id');
    }
}
