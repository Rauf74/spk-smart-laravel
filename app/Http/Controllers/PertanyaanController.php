<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pertanyaan;
use App\Models\Kriteria;
use App\Models\Alternatif;

/**
 * Controller untuk mengelola data Pertanyaan.
 * 
 * Pertanyaan digunakan dalam form penilaian.
 * Setiap pertanyaan terkait dengan:
 * - Satu kriteria (apa yang dinilai)
 * - Satu alternatif (siapa yang dinilai)
 */
class PertanyaanController extends Controller
{
    /**
     * Tampilkan daftar semua pertanyaan.
     */
    public function index()
    {
        // Ambil dengan relasi untuk menampilkan nama kriteria dan alternatif
        $pertanyaans = Pertanyaan::with(['kriteria', 'alternatif'])->get();
        return view('pertanyaan.index', compact('pertanyaans'));
    }

    /**
     * Tampilkan form tambah pertanyaan.
     */
    public function create()
    {
        $kriterias = Kriteria::all();
        $alternatifs = Alternatif::all();
        return view('pertanyaan.create', compact('kriterias', 'alternatifs'));
    }

    /**
     * Simpan pertanyaan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'id_alternatif' => 'required|exists:alternatif,id_alternatif',
            'teks_pertanyaan' => 'required|string',
        ]);

        Pertanyaan::create($request->all());

        return redirect()
            ->route('pertanyaan.index')
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail pertanyaan (tidak digunakan).
     */
    public function show(string $id)
    {
        // Belum diimplementasi
    }

    /**
     * Tampilkan form edit pertanyaan.
     */
    public function edit(string $id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);
        $kriterias = Kriteria::all();
        $alternatifs = Alternatif::all();
        return view('pertanyaan.edit', compact('pertanyaan', 'kriterias', 'alternatifs'));
    }

    /**
     * Update data pertanyaan di database.
     */
    public function update(Request $request, string $id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);

        $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'id_alternatif' => 'required|exists:alternatif,id_alternatif',
            'teks_pertanyaan' => 'required|string',
        ]);

        $pertanyaan->update($request->all());

        return redirect()
            ->route('pertanyaan.index')
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Hapus pertanyaan dari database.
     */
    public function destroy(string $id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);
        $pertanyaan->delete();

        return redirect()
            ->route('pertanyaan.index')
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
