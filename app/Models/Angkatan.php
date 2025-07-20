<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Angkatan extends Model
{
    protected $primaryKey = 'id'; 
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'tahun',
    ];

    
    public function kelas(){
        return $this->hasMany(Kelas::class, 'id_angkatan', 'id');
     }

}
