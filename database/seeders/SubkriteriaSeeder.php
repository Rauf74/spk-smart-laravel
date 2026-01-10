<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subkriteria;

/**
 * Seeder untuk data Subkriteria.
 */
class SubkriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $subkriterias = [
            // K1 - Minat dan Bakat
            ['id_subkriteria' => 1, 'id_kriteria' => 1, 'nama_subkriteria' => 'Tidak Sesuai', 'nilai' => 1.00],
            ['id_subkriteria' => 2, 'id_kriteria' => 1, 'nama_subkriteria' => 'Kurang Sesuai', 'nilai' => 2.00],
            ['id_subkriteria' => 3, 'id_kriteria' => 1, 'nama_subkriteria' => 'Cukup Sesuai', 'nilai' => 3.00],
            ['id_subkriteria' => 4, 'id_kriteria' => 1, 'nama_subkriteria' => 'Sesuai', 'nilai' => 4.00],
            ['id_subkriteria' => 5, 'id_kriteria' => 1, 'nama_subkriteria' => 'Sangat Sesuai', 'nilai' => 5.00],

            // K2 - Prestasi Akademik
            ['id_subkriteria' => 6, 'id_kriteria' => 2, 'nama_subkriteria' => 'Tidak Baik', 'nilai' => 1.00],
            ['id_subkriteria' => 7, 'id_kriteria' => 2, 'nama_subkriteria' => 'Kurang Baik', 'nilai' => 2.00],
            ['id_subkriteria' => 8, 'id_kriteria' => 2, 'nama_subkriteria' => 'Cukup Baik', 'nilai' => 3.00],
            ['id_subkriteria' => 9, 'id_kriteria' => 2, 'nama_subkriteria' => 'Baik', 'nilai' => 4.00],
            ['id_subkriteria' => 10, 'id_kriteria' => 2, 'nama_subkriteria' => 'Sangat Baik', 'nilai' => 5.00],

            // K3 - Motivasi dan Kesiapan Mental
            ['id_subkriteria' => 11, 'id_kriteria' => 3, 'nama_subkriteria' => 'Tidak Termotivasi', 'nilai' => 1.00],
            ['id_subkriteria' => 12, 'id_kriteria' => 3, 'nama_subkriteria' => 'Kurang Termotivasi', 'nilai' => 2.00],
            ['id_subkriteria' => 13, 'id_kriteria' => 3, 'nama_subkriteria' => 'Cukup Termotivasi', 'nilai' => 3.00],
            ['id_subkriteria' => 14, 'id_kriteria' => 3, 'nama_subkriteria' => 'Termotivasi', 'nilai' => 4.00],
            ['id_subkriteria' => 15, 'id_kriteria' => 3, 'nama_subkriteria' => 'Sangat Termotivasi', 'nilai' => 5.00],

            // K4 - Kepribadian dan Kecocokan Psikologis
            ['id_subkriteria' => 16, 'id_kriteria' => 4, 'nama_subkriteria' => 'Tidak Cocok', 'nilai' => 1.00],
            ['id_subkriteria' => 17, 'id_kriteria' => 4, 'nama_subkriteria' => 'Kurang Cocok', 'nilai' => 2.00],
            ['id_subkriteria' => 18, 'id_kriteria' => 4, 'nama_subkriteria' => 'Cukup Cocok', 'nilai' => 3.00],
            ['id_subkriteria' => 19, 'id_kriteria' => 4, 'nama_subkriteria' => 'Cocok', 'nilai' => 4.00],
            ['id_subkriteria' => 20, 'id_kriteria' => 4, 'nama_subkriteria' => 'Sangat Cocok', 'nilai' => 5.00],

            // K5 - Kemampuan Interpersonal dan Soft Skills
            ['id_subkriteria' => 21, 'id_kriteria' => 5, 'nama_subkriteria' => 'Tidak Mendukung', 'nilai' => 1.00],
            ['id_subkriteria' => 22, 'id_kriteria' => 5, 'nama_subkriteria' => 'Kurang Mendukung', 'nilai' => 2.00],
            ['id_subkriteria' => 23, 'id_kriteria' => 5, 'nama_subkriteria' => 'Cukup Mendukung', 'nilai' => 3.00],
            ['id_subkriteria' => 24, 'id_kriteria' => 5, 'nama_subkriteria' => 'Mendukung', 'nilai' => 4.00],
            ['id_subkriteria' => 25, 'id_kriteria' => 5, 'nama_subkriteria' => 'Sangat Mendukung', 'nilai' => 5.00],

            // K6 - Tester K (Cost)
            ['id_subkriteria' => 30, 'id_kriteria' => 22, 'nama_subkriteria' => 'Tidak Baik', 'nilai' => 1.00],
            ['id_subkriteria' => 31, 'id_kriteria' => 22, 'nama_subkriteria' => 'Kurang Baik', 'nilai' => 2.00],
            ['id_subkriteria' => 32, 'id_kriteria' => 22, 'nama_subkriteria' => 'Baik', 'nilai' => 3.00],
            ['id_subkriteria' => 33, 'id_kriteria' => 22, 'nama_subkriteria' => 'Cukup Baik', 'nilai' => 4.00],
            ['id_subkriteria' => 34, 'id_kriteria' => 22, 'nama_subkriteria' => 'Sangat Baik', 'nilai' => 5.00],
        ];

        foreach ($subkriterias as $sub) {
            Subkriteria::create($sub);
        }
    }
}
