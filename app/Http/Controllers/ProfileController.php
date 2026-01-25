<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Controller untuk halaman Profile user.
 */
class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profile.
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    /**
     * Update data profile user.
     */
    /**
     * Update data profile user.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_user' => 'required|string|max:100',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'nis' => 'nullable|string|max:20',
        ]);

        $user->nama_user = $request->nama_user;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->nis = $request->nis;
        $user->save();

        return redirect()->route('profile')->with('success', 'Profile berhasil diperbarui.');
    }

    /**
     * Update password user.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->password = Hash::make($request->password_baru);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password berhasil diperbarui.');
    }
}
