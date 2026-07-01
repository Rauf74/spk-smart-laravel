<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

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
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_logged_in'] = false;

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
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        // Hash password jika diisi, hapus jika tidak
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

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
