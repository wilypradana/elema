<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Materi extends Model
{
    protected $primaryKey = 'id'; 
    public $incrementing = true; 
    public $timestamps = false; 

     protected $fillable = [
        'judul',
        'deskripsi',
        'id_sesi_belajar',
    ];

    public function sesiBelajar(): BelongsTo
    {
        return $this->belongsTo(SesiBelajar::class);
    }
}
