<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GuruMataPelajaran extends Model
{
    protected $primaryKey = 'id'; // Kolom 'id' sebagai primary key secara default
    public $incrementing = true; // Menggunakan auto-increment untuk kolom 'id'
    public $timestamps = false; 

    protected $fillable = [
        'id_guru',
        'id_mata_pelajaran',
        'slug',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Mengisi slug secara otomatis
            $model->slug = Str::random(10) ?? Str::random(10);
        });
    }
    public function guru(): BelongsTo{
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function mataPelajaran(): BelongsTo{
        return $this->belongsTo(MataPelajaran::class, 'id_mata_pelajaran');
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_guru_mata_pelajaran', 'id');
    }

    public function sesiBelajar(): HasMany{
        return $this->hasMany(SesiBelajar::class, 'id_guru_mata_pelajaran', 'id');
    }
}
