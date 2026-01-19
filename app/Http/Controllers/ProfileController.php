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
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_user' => 'required|string|max:100',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'nis' => 'nullable|string|max:20',
            'password_lama' => 'nullable|string',
            'password_baru' => 'nullable|string|min:6|confirmed',
        ]);

        $user->nama_user = $request->nama_user;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->nis = $request->nis;

        // Update password jika diisi
        if ($request->filled('password_lama') && $request->filled('password_baru')) {
            if (!Hash::check($request->password_lama, $user->password)) {
                return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
            }
            $user->password = Hash::make($request->password_baru);
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile berhasil diperbarui.');
    }
}
