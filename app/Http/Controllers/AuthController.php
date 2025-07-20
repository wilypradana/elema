<?php

namespace App\Http\Controllers;

use Filament\Panel\Concerns\HasAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    // Login Siswa
    public function loginSiswa(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Cek menggunakan guard 'student'
        if (Auth::guard('student')->attempt($credentials, false)) {
            // Regenerate session setelah berhasil login
            $request->session()->regenerate();
            return redirect()->route('filament.siswa.pages.dashboard'); // Arahkan ke dashboard siswa
        }

        // Menyimpan error ke session
        session()->flash('error', 'Login gagal. Periksa email atau password Anda.');

        // Redirect ke halaman login siswa
        return redirect()->route('login');
    }

    // Login Guru
    public function loginGuru(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Cek menggunakan guard 'teacher'
        if (Auth::guard('teacher')->attempt($credentials)) {
            // Regenerate session setelah berhasil login
            $request->session()->regenerate();
            return redirect()->route('filament.guru.pages.dashboard'); // Arahkan ke dashboard guru
        }

        // Menyimpan error ke session
        session()->flash('error', 'Login gagal. Periksa email atau password Anda.');

        // Redirect ke halaman login siswa
        return redirect()->route('login');
    }

}
