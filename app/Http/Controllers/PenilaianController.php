<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Alternatif;
use App\Models\Pertanyaan;
use App\Models\Penilaian;
use App\Models\Subkriteria;
use App\Models\User;

/**
 * Controller untuk mengelola Penilaian.
 */
class PenilaianController extends Controller
{
    /**
     * Tampilkan halaman penilaian.
     * - Siswa: Lihat form kuesioner gabungan
     * - Guru BK: Pilih siswa -> Lihat/Edit penilaian siswa
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $targetUserId = $currentUser->id_user;

        // 1. Logika khusus untuk Siswa: Tampilkan form gabungan (Data Penilaian Saya)
        if ($currentUser->role === 'Siswa') {
            $alternatifs = Alternatif::with([
                'pertanyaan' => function ($q) {
                    $q->join('kriteria', 'pertanyaan.id_kriteria', '=', 'kriteria.id_kriteria')
                        ->select('pertanyaan.*')
                        ->orderBy('kriteria.id_kriteria')
                        ->with(['kriteria.subkriteria']);
                }
            ])->get();

            $existingPenilaians = Penilaian::where('id_user', $targetUserId)
                ->pluck('id_subkriteria', 'id_pertanyaan')
                ->toArray();

            return view('penilaian.siswa_index', compact('alternatifs', 'existingPenilaians'));
        }

        // 2. Logika untuk Guru BK: Dropdown Siswa & Accordion View
        if ($currentUser->role === 'Guru BK') {
            $users = User::where('role', 'Siswa')->get();
            $targetUserId = $request->id_user; // Bisa null jika belum pilih

            $alternatifs = [];
            $existingPenilaians = collect();

            if ($targetUserId) {
                $alternatifs = Alternatif::with(['pertanyaan.kriteria.subkriteria'])->get();
                // Kita ambil full collection untuk diproses di view (grouping by pertanyaan/alternatif)
                $existingPenilaians = Penilaian::where('id_user', $targetUserId)->get();
            }

            return view('penilaian.guru_index', compact('users', 'targetUserId', 'alternatifs', 'existingPenilaians'));
        }

        // Fallback default
        return abort(403);
    }

    /**
     * Delete penilaian spesifik (per alternatif per user)
     */
    public function destroyPerAlternatif(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'id_alternatif' => 'required|exists:alternatif,id_alternatif',
        ]);

        // Pastikan hanya Guru BK yang bisa hapus punya orang lain
        if (Auth::user()->role !== 'Guru BK') {
            return abort(403);
        }

        Penilaian::where('id_user', $request->id_user)
            ->where('id_alternatif', $request->id_alternatif)
            ->delete();

        return back()->with('success', 'Data penilaian berhasil dihapus.');
    }

    /**
     * Tampilkan form penilaian untuk alternatif tertentu.
     * (Legacy / unused for main flow now)
     */
    public function create(string $id_alternatif)
    {
        $alternatif = Alternatif::findOrFail($id_alternatif);
        $pertanyaans = Pertanyaan::with(['kriteria.subkriteria'])
            ->where('id_alternatif', $id_alternatif)
            ->get()
            ->sortBy(fn($p) => $p->kriteria->nama_kriteria);

        return view('penilaian.create', compact('alternatif', 'pertanyaans'));
    }

    /**
     * Simpan penilaian ke database.
     * Support single alternative (Guru) OR batch upload (Siswa).
     */
    public function store(Request $request)
    {
        $request->validate([
            'jawaban' => 'required|array',  // Format: [id_pertanyaan => id_subkriteria]
            'jawaban.*' => 'exists:subkriteria,id_subkriteria',
            // Optional: id_user target (jika Guru BK menilai atas nama siswa)
            'id_user' => 'nullable|exists:users,id_user'
        ]);

        $currentUser = Auth::user();

        // Tentukan user target: jika Guru BK submit & ada id_user, pakai itu. Jika Siswa, pakai Auth::id().
        $targetUserId = $currentUser->role === 'Guru BK' && $request->has('id_user')
            ? $request->id_user
            : $currentUser->id_user;

        DB::beginTransaction();
        try {
            $submittedPertanyaanIds = array_keys($request->jawaban);

            // Hapus penilaian lama untuk pertanyaan-pertanyaan ini (untuk user target)
            Penilaian::where('id_user', $targetUserId)
                ->whereIn('id_pertanyaan', $submittedPertanyaanIds)
                ->delete();

            foreach ($request->jawaban as $idPertanyaan => $idSubkriteria) {
                $pertanyaan = Pertanyaan::find($idPertanyaan);
                $subkriteria = Subkriteria::find($idSubkriteria);

                if (!$pertanyaan || !$subkriteria)
                    continue;

                Penilaian::create([
                    'id_user' => $targetUserId,
                    'id_alternatif' => $pertanyaan->id_alternatif,
                    'id_kriteria' => $pertanyaan->id_kriteria,
                    'id_pertanyaan' => $idPertanyaan,
                    'id_subkriteria' => $idSubkriteria,
                    'jawaban' => $subkriteria->nilai,
                ]);
            }

            DB::commit();

            if ($currentUser->role === 'Siswa') {
                return redirect()->route('perangkingan.index')->with('success', 'Penilaian berhasil disimpan.');
            } else {
                // Guru BK stay on page (maintain query string)
                return redirect()->route('penilaian.index', ['id_user' => $targetUserId])->with('success', 'Penilaian berhasil disimpan.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
