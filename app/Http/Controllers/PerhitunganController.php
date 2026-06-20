<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\SmartCalculationService;

/**
 * Controller untuk halaman Perhitungan SMART.
 *
 * Menggunakan SmartCalculationService untuk perhitungan.
 * Halaman ini menampilkan detail: bobot normalisasi,
 * nilai per kriteria, utility, dan nilai akhir.
 */
class PerhitunganController extends Controller
{
    /**
     * Tampilkan halaman perhitungan SMART.
     */
    public function index(Request $request, SmartCalculationService $smartService)
    {
        $currentUser = Auth::user();
        $targetUserId = $currentUser->id_user;
        $users = [];

        // Logika untuk Guru BK: Bisa pilih siswa
        if ($currentUser->role === 'Guru BK') {
            $users = User::where('role', 'Siswa')->get();

            if ($request->has('id_user')) {
                $targetUserId = $request->id_user;
            } elseif ($users->count() > 0) {
                $targetUserId = $users->first()->id_user;
            }
        }

        // Jalankan perhitungan SMART via service
        $result = $smartService->calculate($targetUserId);

        return view('perhitungan.index', [
            'kriterias'    => $result['kriterias'],
            'hasil'        => $result['hasil'],
            'users'        => $users,
            'targetUserId' => $targetUserId,
        ]);
    }
}
