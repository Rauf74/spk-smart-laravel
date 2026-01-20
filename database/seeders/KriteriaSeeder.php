<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kriteria;

/**
 * Seeder untuk data Kriteria.
 * Data sesuai dengan spk_smart_pg_neon.sql
 */
class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $kriterias = [
            ['id_kriteria' => 1, 'kode_kriteria' => 'K1', 'nama_kriteria' => 'Minat dan Bakat', 'jenis' => 'Benefit', 'bobot' => 30.00],
            ['id_kriteria' => 2, 'kode_kriteria' => 'K2', 'nama_kriteria' => 'Prestasi Akademik', 'jenis' => 'Benefit', 'bobot' => 25.00],
            ['id_kriteria' => 3, 'kode_kriteria' => 'K3', 'nama_kriteria' => 'Motivasi dan Kesiapan Mental', 'jenis' => 'Benefit', 'bobot' => 20.00],
            ['id_kriteria' => 4, 'kode_kriteria' => 'K4', 'nama_kriteria' => 'Kepribadian dan Kecocokan Psikologis', 'jenis' => 'Benefit', 'bobot' => 15.00],
            ['id_kriteria' => 5, 'kode_kriteria' => 'K5', 'nama_kriteria' => 'Kemampuan Interpersonal dan Soft Skills', 'jenis' => 'Benefit', 'bobot' => 10.00],
        ];

        foreach ($kriterias as $kriteria) {
            Kriteria::create($kriteria);
        }
    }
}
