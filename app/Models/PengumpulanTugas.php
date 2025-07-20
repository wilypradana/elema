<?php

namespace App\Models;

use App\Models\FilePengumpulanTugas;
use App\Models\Siswa;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PengumpulanTugas extends Model
{
    protected $fillable = [
        'id_tugas',
        'id_siswa',
        'nilai',
        'slug',
    ];
    
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Mengisi slug secara otomatis
            $model->slug = Str::random(10);
        });
    }
    
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'id_tugas', 'id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    public function filePengumpulanTugas(): HasMany
    {
        return $this->hasMany(FilePengumpulanTugas::class,'pengumpulan_tugas_id', 'id');
    }
}
