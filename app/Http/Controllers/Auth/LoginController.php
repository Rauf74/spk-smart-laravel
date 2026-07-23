<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
     * Quick login demo (Auto generate user jika belum ada di database).
     */
    public function quickLogin(Request $request, ?string $role = null)
    {
        $targetRole = $role ?? $request->input('role', 'guru');
        $isSiswa = strtolower($targetRole) === 'siswa';

        if ($isSiswa) {
            $user = User::where('role', 'Siswa')->first();
            if (!$user) {
                $user = User::create([
                    'nama_user' => 'Siswa Demo',
                    'username' => 'siswa01',
                    'password' => Hash::make('password'),
                    'role' => 'Siswa',
                    'jenis_kelamin' => 'Laki-laki',
                    'nis' => '10001',
                    'is_logged_in' => false,
                ]);
            }
        } else {
            $user = User::where('role', 'Guru BK')->first();
            if (!$user) {
                $user = User::create([
                    'nama_user' => 'Guru BK Demo',
                    'username' => 'gurubk',
                    'password' => Hash::make('password'),
                    'role' => 'Guru BK',
                    'jenis_kelamin' => 'Laki-laki',
                    'is_logged_in' => false,
                ]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();
        $user->is_logged_in = true;
        $user->save();

        return redirect()->intended('/')->with('login_success', true);
    }

    /**
     * Generate user demo acak secara dinamis via AJAX, lalu login otomatis.
     */
    public function quickGenerate(Request $request)
    {
        try {
            $roleInput = $request->input('role', 'guru');
            $isSiswa = strtolower($roleInput) === 'siswa';

            $randomSuffix = Str::lower(Str::random(5));
            $rawPassword = 'pass' . rand(1000, 9999);

            if ($isSiswa) {
                $rawUsername = 'siswa_' . $randomSuffix;
                $user = User::create([
                    'nama_user' => 'Siswa Demo #' . rand(100, 999),
                    'username'  => $rawUsername,
                    'password'  => Hash::make($rawPassword),
                    'role'      => 'Siswa',
                    'jenis_kelamin' => 'Laki-laki',
                    'nis'       => (string) rand(10000, 99999),
                    'is_logged_in' => false,
                ]);
            } else {
                $rawUsername = 'guru_' . $randomSuffix;
                $user = User::create([
                    'nama_user' => 'Guru BK Demo #' . rand(100, 999),
                    'username'  => $rawUsername,
                    'password'  => Hash::make($rawPassword),
                    'role'      => 'Guru BK',
                    'jenis_kelamin' => 'Laki-laki',
                    'is_logged_in' => false,
                ]);
            }

            Auth::login($user);
            $request->session()->regenerate();
            $user->is_logged_in = true;
            $user->save();

            return response()->json([
                'status'    => 'success',
                'nama_user' => $user->nama_user,
                'username'  => $rawUsername,
                'password'  => $rawPassword,
                'role'      => $user->role,
                'redirect'  => url('/'),
            ]);
        } catch (\Throwable $e) {
            \Log::error('[QuickGenerate] ' . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'hint'    => app()->hasDebugModeEnabled()
                    ? $e->getFile() . ':' . $e->getLine()
                    : 'Lihat Render logs untuk detail.',
            ], 500);
        }
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
            return redirect()->intended('/')->with('login_success', true);
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

        // Legacy behavior: Redirect to login after register (No Auto Login)
        return redirect('/login')->with('register_success', $user->nama_user);
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
