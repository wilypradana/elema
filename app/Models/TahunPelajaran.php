<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunPelajaran extends Model
{
    protected $primaryKey = 'id'; 
    public $incrementing = true; 
    public $timestamps = false; 

    protected $fillable = [
        'nama', 'aktif',
    ];


    public function jadwalPelajaran(): HasMany{
        return $this->hasMany(JadwalPelajaran::class,'id_tahun_pelajaran','id');
    }
}
