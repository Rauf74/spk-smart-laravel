<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk autentikasi user (Login & Logout).
 * 
 * Logic ini mengikuti sistem legacy:
 * - Login menggunakan username dan password
 * - Setelah login, flag is_logged_in diset true
 * - Setelah logout, flag is_logged_in diset false
 */
class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login user.
     * 
     * Validasi:
     * - Username dan password wajib diisi
     * - Cek kredensial dengan Auth::attempt()
     * 
     * Jika berhasil:
     * - Regenerate session (security)
     * - Set is_logged_in = true
     * - Redirect ke dashboard
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Coba login
        if (Auth::attempt($credentials)) {
            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            // Update flag is_logged_in (untuk kompatibilitas dengan legacy)
            $user = Auth::user();
            $user->is_logged_in = true;
            $user->save();

            // Redirect ke halaman yang diminta, atau dashboard
            return redirect()->intended('/');
        }

        // Jika gagal, kembali dengan error
        return back()
            ->withErrors(['username' => 'Username atau password salah.'])
            ->onlyInput('username');
    }

    /**
     * Proses logout user.
     * 
     * Langkah:
     * - Set is_logged_in = false
     * - Logout dari Auth
     * - Invalidate session
     * - Regenerate CSRF token
     */
    public function logout(Request $request)
    {
        // Update flag sebelum logout
        $user = Auth::user();
        if ($user) {
            $user->is_logged_in = false;
            $user->save();
        }

        // Logout
        Auth::logout();

        // Bersihkan session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
