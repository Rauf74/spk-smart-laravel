<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kriteria;
use App\Models\Alternatif;

/**
 * Controller untuk halaman Perhitungan SMART.
 * 
 * Metode SMART (Simple Multi-Attribute Rating Technique):
 * 1. Normalisasi bobot kriteria (bobot / total bobot)
 * 2. Hitung nilai utility tiap alternatif per kriteria
 * 3. Kalikan utility dengan bobot normalisasi
 * 4. Jumlahkan untuk dapat nilai akhir
 */
class PerhitunganController extends Controller
{
    /**
     * Tampilkan halaman perhitungan SMART.
     * 
     * Logika ini sama persis dengan fungsi legacy:
     * - getCombinedKriteriaData() untuk normalisasi bobot
     * - getNilaiUtilityAlternatif() untuk hitung utility
     * - getNilaiAkhirAlternatif() untuk nilai akhir
     */
    public function index()
    {
        // Ambil ID user yang sedang login
        $userId = Auth::id();

        // ==============================================
        // STEP 1: Ambil data kriteria & hitung normalisasi bobot
        // Rumus: normalisasi = bobot / total_bobot
        // ==============================================
        $kriterias = Kriteria::orderBy('kode_kriteria')->get();
        $totalBobot = $kriterias->sum('bobot');

        // Tambahkan property 'normalisasi' ke setiap kriteria
        $kriterias->transform(function ($kriteria) use ($totalBobot) {
            $kriteria->normalisasi = $totalBobot > 0
                ? round($kriteria->bobot / $totalBobot, 4)
                : 0;
            return $kriteria;
        });

        // ==============================================
        // STEP 2: Ambil data alternatif
        // ==============================================
        $alternatifs = Alternatif::orderBy('kode_alternatif')->get();

        // ==============================================
        // STEP 3: Cari nilai MIN dan MAX per kriteria
        // Ini dibutuhkan untuk rumus utility
        // Scope: hanya dari penilaian user yang login
        // ==============================================
        $minMaxPerKriteria = $this->hitungMinMaxPerKriteria($kriterias, $userId);

        // ==============================================
        // STEP 4: Hitung utility dan nilai akhir tiap alternatif
        // ==============================================
        $hasil = $alternatifs->map(function ($alternatif) use ($userId, $kriterias, $minMaxPerKriteria) {
            return $this->hitungNilaiAlternatif($alternatif, $userId, $kriterias, $minMaxPerKriteria);
        });

        // Filter: hanya tampilkan yang sudah ada penilaiannya
        $hasil = $hasil->filter(function ($alt) {
            return $alt->nilai_akhir > 0;
        });

        return view('perhitungan.index', compact('kriterias', 'hasil'));
    }

    /**
     * Hitung nilai MIN dan MAX untuk setiap kriteria.
     * Data diambil dari tabel penilaian (join subkriteria).
     * 
     * @param Collection $kriterias - Daftar kriteria
     * @param int $userId - ID user yang login
     * @return array - Format: [id_kriteria => ['min' => x, 'max' => y]]
     */
    private function hitungMinMaxPerKriteria($kriterias, $userId)
    {
        $result = [];

        foreach ($kriterias as $kriteria) {
            // Ambil semua nilai dari penilaian user untuk kriteria ini
            $nilaiList = DB::table('penilaian')
                ->join('subkriteria', 'penilaian.id_subkriteria', '=', 'subkriteria.id_subkriteria')
                ->where('penilaian.id_user', $userId)
                ->where('penilaian.id_kriteria', $kriteria->id_kriteria)
                ->pluck('subkriteria.nilai')
                ->toArray();

            // Jika ada data, cari min dan max
            if (!empty($nilaiList)) {
                $result[$kriteria->id_kriteria] = [
                    'min' => min($nilaiList),
                    'max' => max($nilaiList),
                ];
            } else {
                $result[$kriteria->id_kriteria] = [
                    'min' => 0,
                    'max' => 0,
                ];
            }
        }

        return $result;
    }

    /**
     * Hitung nilai utility dan nilai akhir untuk satu alternatif.
     * 
     * Rumus Utility:
     * - Benefit: (nilai - min) / (max - min)
     * - Cost:    (max - nilai) / (max - min)
     * 
     * Rumus Nilai Akhir:
     * - Sum dari (utility * normalisasi bobot) untuk semua kriteria
     * 
     * @param Alternatif $alternatif
     * @param int $userId
     * @param Collection $kriterias
     * @param array $minMaxPerKriteria
     * @return Alternatif - Dengan tambahan property: utilities, nilai_akhir
     */
    private function hitungNilaiAlternatif($alternatif, $userId, $kriterias, $minMaxPerKriteria)
    {
        // Ambil semua penilaian user untuk alternatif ini
        $penilaianList = DB::table('penilaian')
            ->join('subkriteria', 'penilaian.id_subkriteria', '=', 'subkriteria.id_subkriteria')
            ->where('penilaian.id_user', $userId)
            ->where('penilaian.id_alternatif', $alternatif->id_alternatif)
            ->get();

        $utilities = [];
        $nilaiAkhir = 0;

        foreach ($kriterias as $kriteria) {
            // Cari nilai untuk kriteria ini
            $penilaian = $penilaianList->firstWhere('id_kriteria', $kriteria->id_kriteria);
            $nilai = $penilaian ? $penilaian->nilai : 0;

            // Ambil min dan max untuk kriteria ini
            $min = $minMaxPerKriteria[$kriteria->id_kriteria]['min'];
            $max = $minMaxPerKriteria[$kriteria->id_kriteria]['max'];

            // Hitung utility
            $utility = $this->hitungUtility($nilai, $min, $max, $kriteria->jenis);
            $utilities[$kriteria->kode_kriteria] = $utility;

            // Tambahkan ke nilai akhir: utility * normalisasi
            $nilaiAkhir += ($utility * $kriteria->normalisasi);
        }

        // Simpan hasil ke object alternatif
        $alternatif->utilities = $utilities;
        $alternatif->nilai_akhir = round($nilaiAkhir, 4);

        return $alternatif;
    }

    /**
     * Hitung nilai utility untuk satu nilai.
     * 
     * @param float $nilai - Nilai yang akan dihitung
     * @param float $min - Nilai minimum dalam dataset
     * @param float $max - Nilai maksimum dalam dataset
     * @param string $jenis - 'Benefit' atau 'Cost'
     * @return float - Nilai utility (0-1)
     */
    private function hitungUtility($nilai, $min, $max, $jenis)
    {
        // Jika min == max, utility = 1 (semua nilai sama)
        if ($max == $min) {
            return 1;
        }

        // Rumus utility berbeda untuk Benefit dan Cost
        if ($jenis === 'Benefit') {
            // Benefit: semakin tinggi semakin bagus
            return round(($nilai - $min) / ($max - $min), 4);
        } else {
            // Cost: semakin rendah semakin bagus
            return round(($max - $nilai) / ($max - $min), 4);
        }
    }
}
