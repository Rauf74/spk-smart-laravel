<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alternatif;

/**
 * Controller untuk mengelola data Alternatif.
 * 
 * Alternatif adalah pilihan yang akan dinilai/diranking.
 * Dalam konteks SPK ini, alternatif adalah Program Studi.
 * Contoh: Teknik Informatika, Sistem Informasi, dll.
 */
class AlternatifController extends Controller
{
    /**
     * Tampilkan daftar semua alternatif.
     */
    public function index()
    {
        $alternatifs = Alternatif::all();
        return view('alternatif.index', compact('alternatifs'));
    }

    /**
     * Tampilkan form tambah alternatif baru.
     */
    public function create()
    {
        return view('alternatif.create');
    }

    /**
     * Simpan alternatif baru ke database.
     * 
     * Kode dan nama alternatif harus unik.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_alternatif' => 'required|max:10|unique:alternatif,kode_alternatif',
            'nama_alternatif' => 'required|max:100|unique:alternatif,nama_alternatif',
        ]);

        Alternatif::create($request->all());

        return redirect()
            ->route('alternatif.index')
            ->with('success', 'Alternatif berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail alternatif (tidak digunakan).
     */
    public function show(string $id)
    {
        // Belum diimplementasi
    }

    /**
     * Tampilkan form edit alternatif.
     */
    public function edit(string $id)
    {
        $alternatif = Alternatif::findOrFail($id);
        return view('alternatif.edit', compact('alternatif'));
    }

    /**
     * Update data alternatif di database.
     */
    public function update(Request $request, string $id)
    {
        $alternatif = Alternatif::findOrFail($id);

        $request->validate([
            'kode_alternatif' => 'required|max:10|unique:alternatif,kode_alternatif,' . $id . ',id_alternatif',
            'nama_alternatif' => 'required|max:100|unique:alternatif,nama_alternatif,' . $id . ',id_alternatif',
        ]);

        $alternatif->update($request->all());

        return redirect()
            ->route('alternatif.index')
            ->with('success', 'Alternatif berhasil diperbarui.');
    }

    /**
     * Hapus alternatif dari database.
     * 
     * Catatan: Pertanyaan dan penilaian terkait akan ikut terhapus (cascade delete).
     */
    public function destroy(string $id)
    {
        $alternatif = Alternatif::findOrFail($id);
        $alternatif->delete();

        return redirect()
            ->route('alternatif.index')
            ->with('success', 'Alternatif berhasil dihapus.');
    }
}
