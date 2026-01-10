<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Alternatif;
use App\Models\Pertanyaan;
use App\Models\Penilaian;
use App\Models\Subkriteria;

/**
 * Controller untuk mengelola Penilaian.
 * 
 * Penilaian adalah proses siswa menjawab pertanyaan untuk setiap alternatif.
 * Jawaban disimpan berdasarkan subkriteria yang dipilih.
 */
class PenilaianController extends Controller
{
    /**
     * Tampilkan daftar alternatif dengan status penilaian.
     * 
     * Menunjukkan alternatif mana yang sudah dinilai user dan mana yang belum.
     */
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua alternatif, tambahkan status apakah sudah dinilai
        $alternatifs = Alternatif::all()->map(function ($alternatif) use ($userId) {
            // Cek apakah user sudah pernah menilai alternatif ini
            $sudahDinilai = Penilaian::where('id_alternatif', $alternatif->id_alternatif)
                ->where('id_user', $userId)
                ->exists();

            $alternatif->status_penilaian = $sudahDinilai;
            return $alternatif;
        });

        return view('penilaian.index', compact('alternatifs'));
    }

    /**
     * Tampilkan form penilaian untuk alternatif tertentu.
     * 
     * Form berisi pertanyaan-pertanyaan yang harus dijawab.
     * Setiap pertanyaan punya pilihan jawaban berupa subkriteria.
     */
    public function create(string $id_alternatif)
    {
        $alternatif = Alternatif::findOrFail($id_alternatif);

        // Ambil pertanyaan untuk alternatif ini, beserta kriteria dan subkriterianya
        $pertanyaans = Pertanyaan::with(['kriteria.subkriteria'])
            ->where('id_alternatif', $id_alternatif)
            ->get()
            ->sortBy(function ($p) {
                return $p->kriteria->nama_kriteria;
            });

        return view('penilaian.create', compact('alternatif', 'pertanyaans'));
    }

    /**
     * Simpan penilaian ke database.
     * 
     * Strategi: Hapus penilaian lama (jika ada), lalu insert yang baru.
     * Ini memungkinkan user mengulang penilaian.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_alternatif' => 'required|exists:alternatif,id_alternatif',
            'jawaban' => 'required|array',  // Format: [id_pertanyaan => id_subkriteria]
            'jawaban.*' => 'exists:subkriteria,id_subkriteria',
        ]);

        $userId = Auth::id();
        $idAlternatif = $request->id_alternatif;

        // Gunakan transaction untuk menjaga konsistensi data
        DB::beginTransaction();

        try {
            // Hapus penilaian lama untuk alternatif ini (jika ada)
            Penilaian::where('id_user', $userId)
                ->where('id_alternatif', $idAlternatif)
                ->delete();

            // Simpan penilaian baru
            foreach ($request->jawaban as $idPertanyaan => $idSubkriteria) {
                // Ambil data relasi yang dibutuhkan
                $pertanyaan = Pertanyaan::find($idPertanyaan);
                $subkriteria = Subkriteria::find($idSubkriteria);

                // Insert ke tabel penilaian
                Penilaian::create([
                    'id_user' => $userId,
                    'id_alternatif' => $idAlternatif,
                    'id_kriteria' => $pertanyaan->id_kriteria,
                    'id_pertanyaan' => $idPertanyaan,
                    'id_subkriteria' => $idSubkriteria,
                    'jawaban' => $subkriteria->nilai,  // Nilai numerik dari subkriteria
                ]);
            }

            DB::commit();

            return redirect()
                ->route('penilaian.index')
                ->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Tampilkan detail penilaian (tidak digunakan).
     */
    public function show(string $id)
    {
        // Belum diimplementasi
    }

    /**
     * Redirect ke form create untuk mengulang penilaian.
     */
    public function edit(string $id)
    {
        return redirect()->route('penilaian.create', $id);
    }

    /**
     * Update penilaian (tidak digunakan, pakai store dengan override).
     */
    public function update(Request $request, string $id)
    {
        // Tidak digunakan - penilaian diupdate via store
    }

    /**
     * Hapus penilaian (tidak digunakan).
     */
    public function destroy(string $id)
    {
        // Belum diimplementasi
    }
}
