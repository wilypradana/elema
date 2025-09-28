<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;

class RekapController extends Controller
{
   
public function index()
{
    $gurus = \App\Models\Guru::query()
        ->withCount('sesiBelajars') // boleh dipakai atau dihapus kalau gak perlu
        ->with([
            'sesiBelajars' => fn($q) =>
                $q->with('kelas')        // biar bisa tampilkan nama kelas
                  ->orderByDesc('id')    // urut terbaru dulu; bebas kalau mau diubah
        ])
        ->orderBy('name') // ganti ke 'nama' kalau kolommu 'nama'
        ->get();

    return view('recapsesi', compact('gurus'));
}

}
