<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani upaya login dari pengguna.
     */
    public function login(Request $request)
    {
        // 1. Validasi input dari form
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 2. Lakukan upaya autentikasi
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // ================================================================
            // PERUBAHAN UTAMA DI SINI
            // Kita menggunakan redirect()->route() yang lebih langsung untuk
            // menghindari potensi masalah dengan redirect()->intended().
            // ================================================================
            return redirect()->route('landing'); 
        }

        // 3. Jika autentikasi gagal
        return back()->withErrors([
            'username' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('username');
    }

    /**
     * Menangani proses logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan ke halaman login setelah logout
        return redirect()->route('login');
    }
}
