<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\SmartCalculationService;

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

        return view('perangkingan.index', [
            'kriterias'         => $kriterias,
            'hasil'             => $hasil,
            'users'             => $users,
            'userId'            => $userId,
            'topAlternatif'     => $topAlternatif,
            'persenKecocokan'   => $persenKecocokan,
            'insightKriterias'  => $insightKriterias,
        ]);
    }
}
