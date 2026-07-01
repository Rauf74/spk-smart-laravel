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
use App\Http\Requests\StorePenilaianBatchRequest;

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

        // 1. Logika khusus untuk Siswa: Wizard kuesioner per kriteria
        if ($currentUser->role === 'Siswa') {
            // Ambil semua kriteria + subkriteria
            $kriterias = \App\Models\Kriteria::with('subkriteria')
                ->orderBy('kode_kriteria')
                ->get();

            // Untuk setiap kriteria, ambil pertanyaan dari semua alternatif
            $kriterias->each(function ($kriteria) {
                $kriteria->pertanyaans = Pertanyaan::with('alternatif')
                    ->where('id_kriteria', $kriteria->id_kriteria)
                    ->orderBy('id_alternatif')
                    ->get();
            });

            // Total pertanyaan & pertanyaan terjawab
            $totalPertanyaan = Pertanyaan::count();
            $answeredCount = Penilaian::where('id_user', $targetUserId)->count();
            $progressPersen = $totalPertanyaan > 0
                ? round(($answeredCount / $totalPertanyaan) * 100)
                : 0;

            // existingPenilaians: [id_pertanyaan => id_subkriteria]
            $existingPenilaians = Penilaian::where('id_user', $targetUserId)
                ->pluck('id_subkriteria', 'id_pertanyaan')
                ->toArray();

            // Hitung step mana yang aktif (first incomplete step, inferred dari DB)
            // Kalau semua done, buka step terakhir agar siswa bisa klik Selesai
            $currentStep = 0;
            foreach ($kriterias as $idx => $kriteria) {
                $pertanyaanIds = $kriteria->pertanyaans->pluck('id_pertanyaan')->toArray();
                $answeredInStep = count(array_intersect_key(
                    $existingPenilaians,
                    array_flip($pertanyaanIds)
                ));
                $totalInStep = count($pertanyaanIds);

                if ($answeredInStep < $totalInStep) {
                    $currentStep = $idx;
                    break;
                }

                if ($idx === $kriterias->count() - 1) {
                    $currentStep = $idx;
                }
            }

            return view('penilaian.siswa_index', compact(
                'kriterias', 'existingPenilaians',
                'totalPertanyaan', 'answeredCount', 'progressPersen',
                'currentStep'
            ));
        }

        // 2. Logika untuk Guru BK: Dropdown Siswa & Accordion View
        if ($currentUser->role === 'Guru BK') {
            // Filter kelas (prefix NIS)
            $kelasFilter = trim((string) $request->query('kelas', ''));
            $kelasFilter = $kelasFilter !== '' ? substr(preg_replace('/[^0-9]/', '', $kelasFilter), 0, 5) : '';

            // Query siswa, terapkan filter kelas jika ada
            $usersQuery = User::where('role', 'Siswa')
                ->when($kelasFilter !== '', fn($q) => $q->where('nis', 'LIKE', $kelasFilter . '%'))
                ->orderBy('nama_user');

            $users = $usersQuery->get();

            // Daftar kelas unik (2 digit pertama NIS), diurutkan
            $kelasList = User::where('role', 'Siswa')
                ->whereNotNull('nis')
                ->where('nis', '!=', '')
                ->pluck('nis')
                ->map(fn($nis) => substr(preg_replace('/[^0-9]/', '', (string) $nis), 0, 2))
                ->filter(fn($k) => strlen($k) === 2)
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            $targetUserId = $request->id_user; // Bisa null jika belum pilih

            $alternatifs = [];
            $existingPenilaians = collect();

            if ($targetUserId) {
                $alternatifs = Alternatif::with(['pertanyaan.kriteria.subkriteria'])->get();
                $existingPenilaians = Penilaian::where('id_user', $targetUserId)->get();
            }

            return view('penilaian.guru_index', compact('users', 'targetUserId', 'alternatifs', 'existingPenilaians', 'kelasList', 'kelasFilter'));
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
     * Support partial save (_partial=1 redirects back, else final redirect).
     */
    public function store(StorePenilaianBatchRequest $request)
    {
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
                // Partial save: kembali ke penilaian, jangan redirect ke perangkingan
                if ($request->has('_partial')) {
                    return redirect()->route('penilaian.index')
                        ->with('success', 'Jawaban tersimpan. Lanjutkan mengisi ya.');
                }
                // Final save: redirect ke hasil rekomendasi
                return redirect()->route('perangkingan.index')
                    ->with('success', 'Penilaian selesai! Lihat hasil rekomendasi kamu.');
            } else {
                // Guru BK stay on page (maintain query string)
                return redirect()->route('penilaian.index', ['id_user' => $targetUserId])
                    ->with('success', 'Penilaian berhasil disimpan.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
