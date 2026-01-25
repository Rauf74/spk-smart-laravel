<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kriteria;
use App\Models\Alternatif;
use App\Models\User;

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
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $targetUserId = $currentUser->id_user;
        $users = [];

        // Logika untuk Guru BK: Bisa pilih siswa
        if ($currentUser->role === 'Guru BK') {
            $users = User::where('role', 'Siswa')->get();

            // Jika ada request id_user, pakai itu. Jika tidak, pakai siswa pertama.
            if ($request->has('id_user')) {
                $targetUserId = $request->id_user;
            } elseif ($users->count() > 0) {
                // Default ke siswa pertama jika belum memilih
                $targetUserId = $users->first()->id_user;
            }
        }

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
        // Scope: hanya dari penilaian user yang DIPILIH (targetUserId)
        // ==============================================
        $minMaxPerKriteria = $this->hitungMinMaxPerKriteria($kriterias, $targetUserId);

        // ==============================================
        // STEP 4: Hitung utility dan nilai akhir tiap alternatif
        // ==============================================
        $hasil = $alternatifs->map(function ($alternatif) use ($targetUserId, $kriterias, $minMaxPerKriteria) {
            return $this->hitungNilaiAlternatif($alternatif, $targetUserId, $kriterias, $minMaxPerKriteria);
        });

        // Filter: hanya tampilkan yang sudah ada penilaiannya (nilai akhir > 0)
        // Atau jika kita ingin menampilkan tabel kosong (0 0), hilangkan filter ini?
        // User komplain "nol nol aja", berarti mereka ingin melihat hasil yang BENAR.
        // Jika Guru melihat data siswa yang SUDAH menilai, harusnya tidak nol.
        // Jika filter diaktifkan, dan hasilnya nol, tabel akan hilang (masuk ke @else "Belum ada data").
        // Jadi kita keep filter ini.
        $hasil = $hasil->filter(function ($alt) {
            return $alt->nilai_akhir > 0;
        });

        return view('perhitungan.index', compact('kriterias', 'hasil', 'users', 'targetUserId'));
    }

    /**
     * Hitung nilai MIN dan MAX untuk setiap kriteria.
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
        $nilaiKriteria = []; // Store raw values for display

        foreach ($kriterias as $kriteria) {
            // Cari nilai untuk kriteria ini
            $penilaian = $penilaianList->firstWhere('id_kriteria', $kriteria->id_kriteria);
            $nilai = $penilaian ? $penilaian->nilai : 0;
            $nilaiKriteria[$kriteria->id_kriteria] = $nilai;

            // Ambil min dan max untuk kriteria ini
            $min = $minMaxPerKriteria[$kriteria->id_kriteria]['min'];
            $max = $minMaxPerKriteria[$kriteria->id_kriteria]['max'];

            // Hitung utility
            $utility = $this->hitungUtility($nilai, $min, $max, $kriteria->jenis);
            $utilities[$kriteria->id_kriteria] = $utility;

            // Tambahkan ke nilai akhir: utility * normalisasi
            $nilaiAkhir += ($utility * $kriteria->normalisasi);
        }

        // Simpan hasil ke object alternatif
        $alternatif->nilai_kriteria = $nilaiKriteria;
        $alternatif->utility = $utilities;
        $alternatif->nilai_akhir = round($nilaiAkhir, 4);

        return $alternatif;
    }

    /**
     * Hitung nilai utility untuk satu nilai.
     */
    private function hitungUtility($nilai, $min, $max, $jenis)
    {
        // Jika min == max, utility = 1 (semua nilai sama), KECUALI jika min/max adalah 0 (belum ada data)
        if ($max == $min) {
            return ($max == 0) ? 0 : 1;
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
