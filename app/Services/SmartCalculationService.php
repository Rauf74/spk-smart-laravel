<?php

namespace App\Services;

use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk perhitungan metode SMART.
 *
 * Metode SMART (Simple Multi-Attribute Rating Technique):
 * 1. Normalisasi bobot kriteria (bobot / total bobot)
 * 2. Hitung nilai min/max per kriteria dari penilaian user
 * 3. Hitung utility tiap alternatif per kriteria (rumus Benefit/Cost)
 * 4. Kalikan utility dengan bobot normalisasi → nilai akhir
 *
 * Service ini menggantikan duplikasi logic antara
 * PerhitunganController dan PerangkinganController.
 */
class SmartCalculationService
{
    /**
     * Jalankan perhitungan SMART penuh untuk satu user.
     *
     * @param  int   $userId
     * @param  bool  $sortDescending  Urutkan hasil dari nilai tertinggi (untuk perangkingan)
     * @return array{ kriterias: Collection, hasil: Collection|array }
     */
    public function calculate(int $userId, bool $sortDescending = false): array
    {
        $kriterias = $this->getKriterias();
        $alternatifs = $this->getAlternatifs($userId);

        // Step 1: Normalisasi bobot
        $kriterias = $this->normalizeBobot($kriterias);

        // Step 2: Hitung min/max per kriteria
        $minMaxPerKriteria = $this->hitungMinMaxPerKriteria($kriterias, $userId);

        // Step 3-4: Hitung utility + nilai akhir setiap alternatif
        $hasil = $alternatifs->map(function (Alternatif $alternatif) use ($userId, $kriterias, $minMaxPerKriteria) {
            return $this->hitungNilaiAlternatif($alternatif, $userId, $kriterias, $minMaxPerKriteria);
        });

        // Filter: hanya yang punya penilaian (nilai_akhir > 0)
        $hasil = $hasil->filter(fn($item) => $item['nilai_akhir'] > 0);

        // Urutkan jika diminta
        if ($sortDescending) {
            $hasil = $hasil->sortByDesc('nilai_akhir')->values()->toArray();
        }

        return [
            'kriterias' => $kriterias,
            'hasil'     => $hasil,
        ];
    }

    /**
     * Ambil semua kriteria terurut.
     */
    public function getKriterias(): Collection
    {
        return Kriteria::orderBy('kode_kriteria')->get();
    }

    /**
     * Ambil semua alternatif terurut.
     *
     * @param  int  $userId  Tidak dipakai untuk filter, disediakan untuk ekstensi nanti.
     */
    public function getAlternatifs(int $userId): Collection
    {
        return Alternatif::orderBy('kode_alternatif')->get();
    }

    /**
     * Normalisasi bobot kriteria.
     *
     * Rumus: normalisasi = bobot / total_bobot
     * Menambahkan property `normalisasi` ke setiap model Kriteria.
     *
     * @param  Collection  $kriterias
     * @return Collection
     */
    public function normalizeBobot(Collection $kriterias): Collection
    {
        $totalBobot = $kriterias->sum('bobot');

        $kriterias->transform(function (Kriteria $kriteria) use ($totalBobot) {
            $kriteria->normalisasi = $totalBobot > 0
                ? round($kriteria->bobot / $totalBobot, 4)
                : 0;

            return $kriteria;
        });

        return $kriterias;
    }

    /**
     * Hitung nilai MIN dan MAX untuk setiap kriteria.
     *
     * Nilai diambil dari tabel penilaian user tertentu,
     * lalu kolom `nilai` dari subkriteria yang dipilih.
     *
     * @param  Collection  $kriterias
     * @param  int         $userId
     * @return array<int, array{ min: float, max: float }>
     */
    public function hitungMinMaxPerKriteria(Collection $kriterias, int $userId): array
    {
        $result = [];

        foreach ($kriterias as $kriteria) {
            $nilaiList = DB::table('penilaian')
                ->join('subkriteria', 'penilaian.id_subkriteria', '=', 'subkriteria.id_subkriteria')
                ->where('penilaian.id_user', $userId)
                ->where('penilaian.id_kriteria', $kriteria->id_kriteria)
                ->pluck('subkriteria.nilai')
                ->toArray();

            if (!empty($nilaiList)) {
                $result[$kriteria->id_kriteria] = [
                    'min' => (float) min($nilaiList),
                    'max' => (float) max($nilaiList),
                ];
            } else {
                $result[$kriteria->id_kriteria] = [
                    'min' => 0.0,
                    'max' => 0.0,
                ];
            }
        }

        return $result;
    }

    /**
     * Hitung nilai utility untuk satu nilai.
     *
     * Benefit: semakin tinggi nilai → semakin tinggi utility.
     * Cost:    semakin rendah nilai → semakin tinggi utility.
     *
     * Edge cases:
     * - max == min && min == 0: belum ada data → return 0
     * - max == min && min != 0: semua nilai sama → return 1
     *
     * @param  float  $nilai
     * @param  float  $min
     * @param  float  $max
     * @param  string $jenis  'Benefit' atau 'Cost'
     * @return float
     */
    public function hitungUtility(float $nilai, float $min, float $max, string $jenis): float
    {
        // Jika min == max, utility = 1 (semua nilai sama)
        // KECUALI jika min/max adalah 0 (belum ada data)
        if ($max == $min) {
            return ($max == 0) ? 0 : 1;
        }

        if ($jenis === 'Benefit') {
            // Benefit: semakin tinggi semakin bagus
            return round(($nilai - $min) / ($max - $min), 4);
        }

        // Cost: semakin rendah semakin bagus
        return round(($max - $nilai) / ($max - $min), 4);
    }

    /**
     * Hitung nilai utility + nilai akhir untuk satu alternatif.
     *
     * @param  Alternatif  $alternatif
     * @param  int         $userId
     * @param  Collection  $kriterias
     * @param  array       $minMaxPerKriteria
     * @return array{ nama_alternatif: string, kode_alternatif: string,
     *                nilai_kriteria: array, utility: array, nilai_akhir: float }
     */
    public function hitungNilaiAlternatif(
        Alternatif $alternatif,
        int $userId,
        Collection $kriterias,
        array $minMaxPerKriteria
    ): array {
        // Ambil semua penilaian user untuk alternatif ini
        $penilaianList = DB::table('penilaian')
            ->join('subkriteria', 'penilaian.id_subkriteria', '=', 'subkriteria.id_subkriteria')
            ->where('penilaian.id_user', $userId)
            ->where('penilaian.id_alternatif', $alternatif->id_alternatif)
            ->get();

        $nilaiKriteria = [];
        $utilities = [];
        $nilaiAkhir = 0.0;

        foreach ($kriterias as $kriteria) {
            // Cari nilai untuk kriteria ini
            $penilaian = $penilaianList->firstWhere('id_kriteria', $kriteria->id_kriteria);
            $nilai = $penilaian ? (float) $penilaian->nilai : 0.0;
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

        return [
            'nama_alternatif' => $alternatif->nama_alternatif,
            'kode_alternatif' => $alternatif->kode_alternatif,
            'nilai_kriteria'   => $nilaiKriteria,
            'utility'          => $utilities,
            'nilai_akhir'      => round($nilaiAkhir, 4),
        ];
    }
}
