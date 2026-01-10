<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Controller untuk mengelola data User.
 * 
 * User memiliki 2 role:
 * - Guru BK: Bisa mengelola semua data (admin)
 * - Siswa: Hanya bisa mengisi penilaian dan melihat hasil
 */
class UserController extends Controller
{
    /**
     * Tampilkan daftar semua user.
     */
    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    /**
     * Tampilkan form tambah user baru.
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Simpan user baru ke database.
     * 
     * Password akan di-hash secara otomatis sebelum disimpan.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_user' => 'required|max:100',
            'username' => 'required|max:50|unique:users,username',
            'password' => 'required|min:6',
            'role' => 'required|in:Guru BK,Siswa',
            'nis' => 'nullable|numeric|unique:users,nis',
        ]);

        // Siapkan data untuk disimpan
        $data = $request->all();
        $data['password'] = Hash::make($request->password);  // Hash password
        $data['is_logged_in'] = false;  // Default: belum login

        // Simpan ke database
        User::create($data);

        return redirect()
            ->route('user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail user (tidak digunakan).
     */
    public function show(string $id)
    {
        // Belum diimplementasi
    }

    /**
     * Tampilkan form edit user.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    /**
     * Update data user di database.
     * 
     * Password hanya diupdate jika diisi (tidak wajib saat edit).
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Aturan validasi dasar
        $rules = [
            'nama_user' => 'required|max:100',
            'username' => 'required|max:50|unique:users,username,' . $id . ',id_user',
            'role' => 'required|in:Guru BK,Siswa',
            'nis' => 'nullable|numeric|unique:users,nis,' . $id . ',id_user',
        ];

        // Password opsional saat edit
        if ($request->filled('password')) {
            $rules['password'] = 'min:6';
        }

        $request->validate($rules);

        // Siapkan data (tanpa password dulu)
        $data = $request->except(['password']);

        // Hash password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update database
        $user->update($data);

        return redirect()
            ->route('user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user dari database.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('user.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
