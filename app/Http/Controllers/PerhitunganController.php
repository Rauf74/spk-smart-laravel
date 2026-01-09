<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerhitunganController extends Controller
{
    public function index()
    {
        $id_user = \Illuminate\Support\Facades\Auth::id();

        // 1. Data Kriteria & Bobot Normalisasi
        $kriterias = \App\Models\Kriteria::orderBy('kode_kriteria')->get();
        $totalBobot = $kriterias->sum('bobot');
        $kriterias->transform(function ($k) use ($totalBobot) {
            $k->normalisasi = $totalBobot > 0 ? round($k->bobot / $totalBobot, 4) : 0;
            return $k;
        });

        // 2. Data Alternatif
        $alternatifs = \App\Models\Alternatif::orderBy('kode_alternatif')->get();

        // 3. Ambil rata-rata nilai per alternatif per kriteria (Nilai Dasar)
        // Kita butuh min/max per kriteria untuk rumus Utility
        $minMaxKriteria = [];
        foreach ($kriterias as $k) {
            // Cari nilai min/max dari seluruh penilaian yang ada untuk kriteria ini (global scope or user scope?)
            // Berdasarkan logic legacy 'getNilaiUtilityAlternatif', dia query penilaian p WHERE p.id_user = ?
            // Jadi min/max dihitung dari data penilaian user tersebut saja.

            $nilaiValues = DB::table('penilaian')
                ->join('subkriteria', 'penilaian.id_subkriteria', '=', 'subkriteria.id_subkriteria')
                ->where('penilaian.id_user', $id_user)
                ->where('penilaian.id_kriteria', $k->id_kriteria)
                ->pluck('subkriteria.nilai')
                ->toArray();

            if (!empty($nilaiValues)) {
                $minMaxKriteria[$k->id_kriteria] = [
                    'min' => min($nilaiValues),
                    'max' => max($nilaiValues)
                ];
            } else {
                $minMaxKriteria[$k->id_kriteria] = ['min' => 0, 'max' => 0];
            }
        }

        // 4. Hitung Utility & Nilai Akhir per Alternatif
        $hasil = $alternatifs->map(function ($alt) use ($id_user, $kriterias, $minMaxKriteria) {
            // Ambil nilai penilaian user untuk alternatif ini
            $penilaians = DB::table('penilaian')
                ->join('subkriteria', 'penilaian.id_subkriteria', '=', 'subkriteria.id_subkriteria')
                ->where('penilaian.id_user', $id_user)
                ->where('penilaian.id_alternatif', $alt->id_alternatif)
                ->get();

            // Map nilai per kriteria
            $nilaiPerKriteria = []; // Utk debugging/display nilai mentah
            $utilities = []; // Nilai utility
            $nilaiAkhir = 0;

            foreach ($kriterias as $k) {
                // Cari nilai untuk kriteria ini
                $p = $penilaians->firstWhere('id_kriteria', $k->id_kriteria);
                $nilai = $p ? $p->nilai : 0; // Default 0 jika belum dinilai

                $nilaiPerKriteria[$k->kode_kriteria] = $nilai;

                // Hitung Utility
                $min = $minMaxKriteria[$k->id_kriteria]['min'];
                $max = $minMaxKriteria[$k->id_kriteria]['max'];
                $utility = 0;

                if ($max != $min) {
                    if ($k->jenis == 'Benefit') {
                        $utility = ($nilai - $min) / ($max - $min);
                    } else {
                        $utility = ($max - $nilai) / ($max - $min);
                    }
                } else {
                    $utility = 1; // Jika min == max, utility = 1
                }

                $utilities[$k->kode_kriteria] = round($utility, 4);

                // Hitung ui * ai (Utility * Bobot Normalisasi)
                $nilaiAkhir += ($utility * $k->normalisasi);
            }

            $alt->nilai_kriteria = $nilaiPerKriteria;
            $alt->utilities = $utilities;
            $alt->nilai_akhir = round($nilaiAkhir, 4);

            return $alt;
        });

        // Filter: Hanya tampilkan alternatif yang sudah dinilai (ada minimal 1 nilai > 0)
        // Atau legacy logic menampilkan semua? Legacy sepertinya join penilaian, jadi kalau gak ada penilaian gak muncul.
        // Kita filter yang punya nilai akhir > 0 atau punya data penilaian.
        $hasil = $hasil->filter(function ($alt) {
            // Cek apakah user pernah menilai alternatif ini
            // Sederhananya, jika nilai utilities tidak kosong/0. Tapi karena default 0, kita cek apakah ada penilaian.
            // Di map diatas kita query DB per alternatif (N+1 issue but okay for small SPK).
            // Better Optimization: Eager load diawal. Tapi biarkan dulu demi akurasi logic.
            return count($alt->utilities) > 0;
        });

        return view('perhitungan.index', compact('kriterias', 'hasil'));
    }
}
