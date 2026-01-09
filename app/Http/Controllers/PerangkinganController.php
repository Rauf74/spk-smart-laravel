<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerangkinganController extends Controller
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

        // 3. Min/Max Logic
        $minMaxKriteria = [];
        foreach ($kriterias as $k) {
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

        // 4. Calculate
        $hasil = $alternatifs->map(function ($alt) use ($id_user, $kriterias, $minMaxKriteria) {
            $penilaians = DB::table('penilaian')
                ->join('subkriteria', 'penilaian.id_subkriteria', '=', 'subkriteria.id_subkriteria')
                ->where('penilaian.id_user', $id_user)
                ->where('penilaian.id_alternatif', $alt->id_alternatif)
                ->get();

            $nilaiAkhir = 0;

            foreach ($kriterias as $k) {
                $p = $penilaians->firstWhere('id_kriteria', $k->id_kriteria);
                $nilai = $p ? $p->nilai : 0;

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
                    $utility = 1;
                }

                $nilaiAkhir += ($utility * $k->normalisasi);
            }

            $alt->nilai_akhir = round($nilaiAkhir, 4);
            return $alt;
        });

        // Filter & Sort DESC for Ranking
        $hasil = $hasil->filter(function ($alt) {
            return $alt->nilai_akhir > 0;
        })->sortByDesc('nilai_akhir')->values();

        return view('perangkingan.index', compact('kriterias', 'hasil'));
    }
}
