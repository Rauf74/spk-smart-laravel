<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pertanyaan;

/**
 * Seeder untuk data Pertanyaan.
 */
class PertanyaanSeeder extends Seeder
{
    public function run(): void
    {
        $pertanyaans = [
            // A1 - Teknik Informatika
            ['id_pertanyaan' => 1, 'id_kriteria' => 1, 'id_alternatif' => 1, 'teks_pertanyaan' => 'Sejauh mana Anda merasa memiliki kreativitas dan kemampuan analitis dalam menciptakan solusi digital melalui logika dan algoritma?'],
            ['id_pertanyaan' => 2, 'id_kriteria' => 2, 'id_alternatif' => 1, 'teks_pertanyaan' => 'Seberapa baik Anda tampil dalam mata pelajaran yang menekankan logika, pemrograman, dan analisis komputasional?'],
            ['id_pertanyaan' => 3, 'id_kriteria' => 3, 'id_alternatif' => 1, 'teks_pertanyaan' => 'Sejauh mana Anda termotivasi untuk mengeksplorasi dunia pemrograman dan menciptakan solusi inovatif melalui logika komputasional?'],
            ['id_pertanyaan' => 4, 'id_kriteria' => 4, 'id_alternatif' => 1, 'teks_pertanyaan' => 'Bagaimana Anda menilai kecocokan kepribadian analitis dan teliti Anda dalam menghadapi tantangan di bidang pemrograman dan logika komputasional?'],
            ['id_pertanyaan' => 5, 'id_kriteria' => 5, 'id_alternatif' => 1, 'teks_pertanyaan' => 'Sejauh mana kemampuan Anda dalam berkolaborasi dan berkomunikasi mendukung aktivitas yang berfokus pada penciptaan solusi digital berbasis logika dan algoritma?'],

            // A2 - Sistem Informasi
            ['id_pertanyaan' => 6, 'id_kriteria' => 1, 'id_alternatif' => 2, 'teks_pertanyaan' => 'Bagaimana ketertarikan Anda dalam mengorganisasi dan mengelola informasi untuk mendukung pengambilan keputusan yang efektif?'],
            ['id_pertanyaan' => 7, 'id_kriteria' => 2, 'id_alternatif' => 2, 'teks_pertanyaan' => 'Bagaimana kinerja Anda dalam mata pelajaran yang mengintegrasikan aspek teknologi dengan pengolahan dan manajemen informasi?'],
            ['id_pertanyaan' => 8, 'id_kriteria' => 3, 'id_alternatif' => 2, 'teks_pertanyaan' => 'Bagaimana tingkat antusiasme Anda dalam memanfaatkan teknologi untuk mengintegrasikan informasi guna mendukung pengambilan keputusan?'],
            ['id_pertanyaan' => 9, 'id_kriteria' => 4, 'id_alternatif' => 2, 'teks_pertanyaan' => 'Sejauh mana karakter Anda mendukung kemampuan dalam mengorganisasi dan mengelola aliran informasi secara efektif?'],
            ['id_pertanyaan' => 10, 'id_kriteria' => 5, 'id_alternatif' => 2, 'teks_pertanyaan' => 'Bagaimana kemampuan interpersonal Anda dalam bekerja sama mendukung integrasi dan pengelolaan informasi secara efektif?'],

            // A3 - Teknik Telekomunikasi
            ['id_pertanyaan' => 11, 'id_kriteria' => 1, 'id_alternatif' => 3, 'teks_pertanyaan' => 'Sejauh mana Anda tertarik untuk merancang dan mengelola infrastruktur komunikasi digital serta mengoptimalkan jaringan interkoneksi?'],
            ['id_pertanyaan' => 12, 'id_kriteria' => 2, 'id_alternatif' => 3, 'teks_pertanyaan' => 'Sejauh mana prestasi Anda mendukung pemahaman tentang teori komunikasi, jaringan, dan infrastruktur digital?'],
            ['id_pertanyaan' => 13, 'id_kriteria' => 3, 'id_alternatif' => 3, 'teks_pertanyaan' => 'Sejauh mana Anda siap menghadapi tantangan yang berkaitan dengan pengelolaan dan pengembangan infrastruktur komunikasi digital?'],
            ['id_pertanyaan' => 14, 'id_kriteria' => 4, 'id_alternatif' => 3, 'teks_pertanyaan' => 'Bagaimana Anda menilai kesesuaian sifat komunikatif dan responsif Anda dalam menghadapi tantangan di dunia komunikasi digital?'],
            ['id_pertanyaan' => 15, 'id_kriteria' => 5, 'id_alternatif' => 3, 'teks_pertanyaan' => 'Sejauh mana keahlian Anda dalam berkomunikasi efektif dapat mendukung pengelolaan jaringan dan infrastruktur komunikasi yang kompleks?'],

            // A4 - Rekayasa Perangkat Lunak
            ['id_pertanyaan' => 16, 'id_kriteria' => 1, 'id_alternatif' => 4, 'teks_pertanyaan' => 'Bagaimana minat dan bakat Anda dalam mengembangkan solusi kreatif melalui perancangan aplikasi dan sistem yang terstruktur?'],
            ['id_pertanyaan' => 17, 'id_kriteria' => 2, 'id_alternatif' => 4, 'teks_pertanyaan' => 'Bagaimana pencapaian Anda dalam mata pelajaran yang menekankan metodologi perancangan sistem dan pengembangan aplikasi secara sistematis?'],
            ['id_pertanyaan' => 18, 'id_kriteria' => 3, 'id_alternatif' => 4, 'teks_pertanyaan' => 'Bagaimana motivasi Anda dalam mendalami proses perancangan dan pengembangan sistem secara terstruktur dan inovatif?'],
            ['id_pertanyaan' => 19, 'id_kriteria' => 4, 'id_alternatif' => 4, 'teks_pertanyaan' => 'Sejauh mana karakter kreatif dan sistematis Anda mendukung kemampuan untuk merancang dan mengembangkan aplikasi secara inovatif?'],
            ['id_pertanyaan' => 20, 'id_kriteria' => 5, 'id_alternatif' => 4, 'teks_pertanyaan' => 'Bagaimana Anda menilai kemampuan kolaboratif Anda dalam mendukung proses perancangan dan pengembangan sistem yang terstruktur dan inovatif?'],
        ];

        foreach ($pertanyaans as $p) {
            Pertanyaan::create($p);
        }
    }
}
