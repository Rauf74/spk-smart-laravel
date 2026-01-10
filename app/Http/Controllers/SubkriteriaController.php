<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subkriteria;
use App\Models\Kriteria;

/**
 * Controller untuk mengelola data Subkriteria.
 * 
 * Subkriteria adalah pilihan nilai untuk setiap kriteria.
 * Contoh untuk kriteria "Akreditasi":
 * - Sangat Baik (nilai: 5)
 * - Baik (nilai: 4)
 * - Cukup (nilai: 3)
 * - Kurang (nilai: 2)
 * - Sangat Kurang (nilai: 1)
 */
class SubkriteriaController extends Controller
{
    /**
     * Tampilkan daftar semua subkriteria.
     */
    public function index()
    {
        // Ambil dengan relasi kriteria untuk menampilkan nama kriteria
        $subkriterias = Subkriteria::with('kriteria')->get();
        return view('subkriteria.index', compact('subkriterias'));
    }

    /**
     * Tampilkan form tambah subkriteria.
     */
    public function create()
    {
        // Ambil daftar kriteria untuk dropdown
        $kriterias = Kriteria::all();
        return view('subkriteria.create', compact('kriterias'));
    }

    /**
     * Simpan subkriteria baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'nama_subkriteria' => 'required|max:50',
            'nilai' => 'required|numeric',
        ]);

        Subkriteria::create($request->all());

        return redirect()
            ->route('subkriteria.index')
            ->with('success', 'Sub Kriteria berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail subkriteria (tidak digunakan).
     */
    public function show(string $id)
    {
        // Belum diimplementasi
    }

    /**
     * Tampilkan form edit subkriteria.
     */
    public function edit(string $id)
    {
        $subkriteria = Subkriteria::findOrFail($id);
        $kriterias = Kriteria::all();
        return view('subkriteria.edit', compact('subkriteria', 'kriterias'));
    }

    /**
     * Update data subkriteria di database.
     */
    public function update(Request $request, string $id)
    {
        $subkriteria = Subkriteria::findOrFail($id);

        $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'nama_subkriteria' => 'required|max:50',
            'nilai' => 'required|numeric',
        ]);

        $subkriteria->update($request->all());

        return redirect()
            ->route('subkriteria.index')
            ->with('success', 'Sub Kriteria berhasil diperbarui.');
    }

    /**
     * Hapus subkriteria dari database.
     */
    public function destroy(string $id)
    {
        $subkriteria = Subkriteria::findOrFail($id);
        $subkriteria->delete();

        return redirect()
            ->route('subkriteria.index')
            ->with('success', 'Sub Kriteria berhasil dihapus.');
    }
}
