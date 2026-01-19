<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Controller untuk autentikasi user (Login, Register & Logout).
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
     * Tampilkan halaman register.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Proses login user.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $user->is_logged_in = true;
            $user->save();
            return redirect()->intended('/');
        }

        return back()
            ->withErrors(['username' => 'Username atau password salah.'])
            ->onlyInput('username');
    }

    /**
     * Proses registrasi user baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama_user' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nis' => 'nullable|string|max:20|unique:users,nis',
        ]);

        $user = User::create([
            'nama_user' => $request->nama_user,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'Siswa',
            'jenis_kelamin' => $request->jenis_kelamin,
            'nis' => $request->nis,
            'is_logged_in' => false,
        ]);

        Auth::login($user);
        $user->is_logged_in = true;
        $user->save();

        return redirect('/');
    }

    /**
     * Proses logout user.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->is_logged_in = false;
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
