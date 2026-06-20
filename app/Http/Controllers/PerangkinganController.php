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

        return view('perangkingan.index', [
            'kriterias' => $result['kriterias'],
            'hasil'     => $result['hasil'],
            'users'     => $users,
            'userId'    => $userId,
        ]);
    }
}
