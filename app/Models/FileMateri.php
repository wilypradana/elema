<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileMateri extends Model
{
    protected $primaryKey = 'id'; 
    public $incrementing = true; 
    public $timestamps = false; 

    protected $fillable = [
        'nama',
        'file',
        'id_sesi_belajar',
    ];


    public function sesiBelajar(): BelongsTo{
        return $this->belongsTo(SesiBelajar::class);
    }
}
