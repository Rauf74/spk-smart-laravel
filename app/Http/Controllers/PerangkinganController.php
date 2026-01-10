<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kriteria;
use App\Models\Alternatif;

/**
 * Controller untuk halaman Perangkingan.
 * 
 * Menampilkan hasil akhir perhitungan SMART yang sudah diurutkan
 * dari nilai tertinggi ke terendah (ranking).
 */
class PerangkinganController extends Controller
{
    /**
     * Tampilkan halaman perangkingan.
     * 
     * Logic sama dengan PerhitunganController, tapi hasilnya diurutkan descending.
     */
    public function index()
    {
        $userId = Auth::id();

        // ==============================================
        // STEP 1: Ambil kriteria dan hitung normalisasi
        // ==============================================
        $kriterias = Kriteria::orderBy('kode_kriteria')->get();
        $totalBobot = $kriterias->sum('bobot');

        $kriterias->transform(function ($k) use ($totalBobot) {
            $k->normalisasi = $totalBobot > 0 ? round($k->bobot / $totalBobot, 4) : 0;
            return $k;
        });

        // ==============================================
        // STEP 2: Ambil alternatif
        // ==============================================
        $alternatifs = Alternatif::orderBy('kode_alternatif')->get();

        // ==============================================
        // STEP 3: Hitung min/max per kriteria
        // ==============================================
        $minMaxPerKriteria = [];
        foreach ($kriterias as $k) {
            $nilaiList = DB::table('penilaian')
                ->join('subkriteria', 'penilaian.id_subkriteria', '=', 'subkriteria.id_subkriteria')
                ->where('penilaian.id_user', $userId)
                ->where('penilaian.id_kriteria', $k->id_kriteria)
                ->pluck('subkriteria.nilai')
                ->toArray();

            $minMaxPerKriteria[$k->id_kriteria] = !empty($nilaiList)
                ? ['min' => min($nilaiList), 'max' => max($nilaiList)]
                : ['min' => 0, 'max' => 0];
        }

        // ==============================================
        // STEP 4: Hitung nilai akhir setiap alternatif
        // ==============================================
        $hasil = $alternatifs->map(function ($alt) use ($userId, $kriterias, $minMaxPerKriteria) {
            $penilaians = DB::table('penilaian')
                ->join('subkriteria', 'penilaian.id_subkriteria', '=', 'subkriteria.id_subkriteria')
                ->where('penilaian.id_user', $userId)
                ->where('penilaian.id_alternatif', $alt->id_alternatif)
                ->get();

            $nilaiAkhir = 0;

            foreach ($kriterias as $k) {
                $p = $penilaians->firstWhere('id_kriteria', $k->id_kriteria);
                $nilai = $p ? $p->nilai : 0;

                $min = $minMaxPerKriteria[$k->id_kriteria]['min'];
                $max = $minMaxPerKriteria[$k->id_kriteria]['max'];

                // Hitung utility
                if ($max != $min) {
                    $utility = ($k->jenis === 'Benefit')
                        ? ($nilai - $min) / ($max - $min)
                        : ($max - $nilai) / ($max - $min);
                } else {
                    $utility = 1;
                }

                $nilaiAkhir += ($utility * $k->normalisasi);
            }

            $alt->nilai_akhir = round($nilaiAkhir, 4);
            return $alt;
        });

        // ==============================================
        // STEP 5: Filter dan urutkan (RANKING)
        // ==============================================
        $hasil = $hasil
            ->filter(fn($alt) => $alt->nilai_akhir > 0)  // Hanya yang sudah dinilai
            ->sortByDesc('nilai_akhir')                   // Urutkan dari tertinggi
            ->values();                                    // Reset index

        return view('perangkingan.index', compact('kriterias', 'hasil'));
    }
}
