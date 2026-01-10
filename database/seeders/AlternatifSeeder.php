<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alternatif;

/**
 * Seeder untuk data Alternatif (Program Studi).
 */
class AlternatifSeeder extends Seeder
{
    public function run(): void
    {
        $alternatifs = [
            ['id_alternatif' => 1, 'kode_alternatif' => 'A1', 'nama_alternatif' => 'Teknik Informatika'],
            ['id_alternatif' => 2, 'kode_alternatif' => 'A2', 'nama_alternatif' => 'Sistem Informasi'],
            ['id_alternatif' => 3, 'kode_alternatif' => 'A3', 'nama_alternatif' => 'Teknik Telekomunikasi'],
            ['id_alternatif' => 4, 'kode_alternatif' => 'A4', 'nama_alternatif' => 'Rekayasa Perangkat Lunak'],
        ];

        foreach ($alternatifs as $alt) {
            Alternatif::create($alt);
        }
    }
}
