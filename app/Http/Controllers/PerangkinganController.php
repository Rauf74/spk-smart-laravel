<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\SmartCalculationService;
use App\Models\CatatanKonseling;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Controller untuk halaman Perangkingan.
 *
 * Menampilkan hasil akhir perhitungan SMART yang sudah diurutkan
 * dari nilai tertinggi ke terendah (ranking).
 *
 * Untuk detail perhitungan, lihat PerhitunganController.
 */
class PerangkinganController extends Controller
{
    /**
     * Tampilkan halaman perangkingan.
     */
    public function index(Request $request, SmartCalculationService $smartService)
    {
        $currentUser = Auth::user();
        $userId = $currentUser->id_user;
        $users = [];

        // Logika untuk Guru BK: Bisa pilih siswa
        if ($currentUser->role === 'Guru BK') {
            $users = User::where('role', 'Siswa')->get();

            if ($request->has('id_user')) {
                $userId = $request->id_user;
            } elseif ($users->count() > 0) {
                $userId = $users->first()->id_user;
            }
        }

        // Jalankan perhitungan SMART via service dengan hasil diurutkan
        $result = $smartService->calculate($userId, sortDescending: true);
        $kriterias = $result['kriterias'];
        $hasil = $result['hasil'];

        // === PREPARE PRIMARY LAYER DATA (hasil awam) ===
        $topAlternatif = null;
        $persenKecocokan = 0;
        $insightKriterias = [];

        if (!empty($hasil)) {
            $topAlternatif = $hasil[0];
            $persenKecocokan = min(100, round($topAlternatif['nilai_akhir'] * 100));

            // Ambil 2 kriteria dengan utility tertinggi di winner
            $utilities = $topAlternatif['utility'];
            arsort($utilities);
            $topTwo = array_slice($utilities, 0, 2, true);

            foreach ($topTwo as $idKriteria => $utilityVal) {
                $kriteria = $kriterias->firstWhere('id_kriteria', $idKriteria);
                if ($kriteria) {
                    $insightKriterias[] = [
                        'nama' => $kriteria->nama_kriteria,
                        'utility' => $utilityVal,
                    ];
                }
            }
        }

        // Ambil catatan konseling jika ada
        $catatanKonseling = CatatanKonseling::where('id_user', $userId)
            ->where('id_guru', $currentUser->id_user)
            ->first();

        return view('perangkingan.index', [
            'kriterias'         => $kriterias,
            'hasil'             => $hasil,
            'users'             => $users,
            'userId'            => $userId,
            'topAlternatif'     => $topAlternatif,
            'persenKecocokan'   => $persenKecocokan,
            'insightKriterias'  => $insightKriterias,
            'catatanKonseling'  => $catatanKonseling,
        ]);
    }

    /**
     * Simpan atau update catatan konseling Guru BK untuk siswa.
     */
    public function storeCatatan(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'catatan' => 'required|string|max:2000',
        ]);

        $guruId = Auth::user()->id_user;

        CatatanKonseling::updateOrCreate(
            [
                'id_user' => $request->id_user,
                'id_guru' => $guruId,
            ],
            [
                'catatan' => $request->catatan,
            ]
        );

        return redirect()->route('perangkingan.index', ['id_user' => $request->id_user])
            ->with('success', 'Catatan konseling berhasil disimpan.');
    }

    /**
     * Export hasil rekomendasi ke PDF.
     */
    public function exportPDF(Request $request, SmartCalculationService $smartService)
    {
        $currentUser = Auth::user();
        $userId = $currentUser->id_user;

        if ($currentUser->role === 'Guru BK' && $request->has('id_user')) {
            $userId = $request->id_user;
        }

        $siswa = User::findOrFail($userId);
        $result = $smartService->calculate($userId, sortDescending: true);
        $kriterias = $result['kriterias'];
        $hasil = $result['hasil'];

        $topAlternatif = $hasil[0] ?? null;
        $persenKecocokan = $topAlternatif ? min(100, round($topAlternatif['nilai_akhir'] * 100)) : 0;

        $pdf = Pdf::loadView('perangkingan.pdf', [
            'siswa' => $siswa,
            'kriterias' => $kriterias,
            'hasil' => $hasil,
            'topAlternatif' => $topAlternatif,
            'persenKecocokan' => $persenKecocokan,
            'tanggal' => now()->format('d F Y'),
        ]);

        return $pdf->download('Hasil-Rekomendasi-' . $siswa->nama_user . '.pdf');
    }
}
