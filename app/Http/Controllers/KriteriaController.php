<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Http\Requests\StoreKriteriaRequest;
use App\Http\Requests\UpdateKriteriaRequest;

/**
 * Controller untuk mengelola data Kriteria.
 * 
 * Kriteria adalah faktor-faktor yang digunakan untuk menilai alternatif.
 * Contoh: Fasilitas, Akreditasi, Biaya, dll.
 * 
 * Setiap kriteria punya:
 * - kode_kriteria: Kode unik (misal: C1, C2)
 * - nama_kriteria: Nama lengkap
 * - jenis: Benefit (semakin tinggi semakin bagus) atau Cost (semakin rendah semakin bagus)
 * - bobot: Persentase kepentingan (total semua kriteria = 100%)
 */
class KriteriaController extends Controller
{
    /**
     * Tampilkan daftar semua kriteria.
     */
    public function index()
    {
        $kriterias = Kriteria::all();
        $totalBobot = $kriterias->sum('bobot');
        return view('kriteria.index', compact('kriterias', 'totalBobot'));
    }

    /**
     * Tampilkan form untuk menambah kriteria baru.
     */
    public function create()
    {
        return view('kriteria.create');
    }

    /**
     * Simpan kriteria baru ke database.
     * 
     * Validasi:
     * - Kode dan nama harus unik
     * - Jenis harus Benefit atau Cost
     * - Bobot 0-100, dan total semua bobot tidak boleh lebih dari 100%
     */
    public function store(StoreKriteriaRequest $request)
    {
        // Cek apakah total bobot tidak melebihi 100%
        $totalBobotSaatIni = Kriteria::sum('bobot');
        $bobotBaru = $request->bobot;

        if (($totalBobotSaatIni + $bobotBaru) > 100) {
            return back()
                ->withErrors(['bobot' => "Total bobot melebihi 100%. Sisa yang tersedia: " . (100 - $totalBobotSaatIni) . "%"])
                ->withInput();
        }

        // Simpan ke database
        Kriteria::create($request->all());

        return redirect()
            ->route('kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail kriteria (tidak digunakan, bisa dikembangkan nanti).
     */
    public function show(string $id)
    {
        // Belum diimplementasi
    }

    /**
     * Tampilkan form edit kriteria.
     */
    public function edit(string $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('kriteria.edit', compact('kriteria'));
    }

    /**
     * Update data kriteria di database.
     * 
     * Sama seperti store, tapi perlu exclude bobot lama saat validasi total.
     */
    public function update(UpdateKriteriaRequest $request, string $id)
    {
        $kriteria = Kriteria::findOrFail($id);

        // Hitung total bobot (tanpa bobot kriteria yang sedang diedit)
        $totalBobotLain = Kriteria::sum('bobot') - $kriteria->bobot;
        $bobotBaru = $request->bobot;

        if (($totalBobotLain + $bobotBaru) > 100) {
            return back()
                ->withErrors(['bobot' => "Total bobot melebihi 100%. Sisa yang tersedia: " . (100 - $totalBobotLain) . "%"])
                ->withInput();
        }

        $kriteria->update($request->validated());

        return redirect()
            ->route('kriteria.index')
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    /**
     * Hapus kriteria dari database.
     * 
     * Catatan: Subkriteria terkait akan ikut terhapus (cascade delete).
     */
    public function destroy(string $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->delete();

        return redirect()
            ->route('kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}
