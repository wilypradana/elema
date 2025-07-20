<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    protected $primaryKey = 'id'; 
    public $incrementing = true; 
    public $timestamps = false; 

    protected $fillable = [
        'nama',
        'kode',
    ];

    public function kelas(): HasMany{
        return $this->hasMany(Kelas::class, 'id_jurusan', 'id');
    }
}
