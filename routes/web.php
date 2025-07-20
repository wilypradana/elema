<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Url;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

Route::get('/template-siswa', function () {
    $filePath = storage_path('app/public/panduan/template-siswa.xlsx'); // Pastikan file ada di lokasi yang tepat

    return Response::download($filePath);
})->name('template-siswa');

Route::get('/template-guru', function () {
    $filePath = storage_path('app/public/panduan/template-guru.xlsx'); // Pastikan file ada di lokasi yang tepat

    return Response::download($filePath);
})->name('template-guru');

Route::get('/template-soal', function () {
    $filePath = storage_path('app/public/panduan/template-soal.xlsx'); // Pastikan file ada di lokasi yang tepat

    return Response::download($filePath);
})->name('template-soal');

Route::get('/rekap-nilai-info', function () {
    $filePath = storage_path('app/public/panduan/panduan-rekap-nilai.pdf'); // Pastikan file ada di lokasi yang tepat

    if (!file_exists($filePath)) {
        abort(404, 'File not found.');
    }

    return Response::file($filePath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="panduan-rekap.pdf"',
    ]);
})->name('panduan-rekap-nilai');

Route::get('/panduan-admin', function () {
    $filePath = storage_path('app/public/panduan/admin.pdf'); // Pastikan file ada di lokasi yang tepat

    if (!file_exists($filePath)) {
        abort(404, 'File not found.');
    }

    return Response::file($filePath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="panduan-rekap.pdf"',
    ]);
})->name('panduan-admin');

Route::post('/login/siswa', [AuthController::class, 'loginSiswa'])->name('login.siswa');
Route::post('/login/guru', [AuthController::class, 'loginGuru'])->name('login.guru');