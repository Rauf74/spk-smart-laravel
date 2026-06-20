<?php

namespace Tests\Unit;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Services\SmartCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk SmartCalculationService.
 *
 * Memastikan logic SMART — normalisasi, utility, min/max,
 * perhitungan nilai akhir, dan ranking — benar.
 */
class SmartCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private SmartCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SmartCalculationService();
    }

    // ============================================================
    // 1. Normalisasi Bobot
    // ============================================================

    #[Test]
    public function normalisasi_total_sama_dengan_satu_saat_bobot_positif(): void
    {
        $kriterias = collect([
            new Kriteria(['bobot' => 40]),
            new Kriteria(['bobot' => 35]),
            new Kriteria(['bobot' => 25]),
        ]);

        $result = $this->service->normalizeBobot($kriterias);

        $total = $result->sum('normalisasi');
        $this->assertEqualsWithDelta(1.0, $total, 0.001, 'Total normalisasi harus ~1.0');
    }

    #[Test]
    public function normalisasi_bernilai_nol_saat_total_bobot_nol(): void
    {
        $kriterias = collect([
            new Kriteria(['bobot' => 0]),
            new Kriteria(['bobot' => 0]),
        ]);

        $result = $this->service->normalizeBobot($kriterias);

        $this->assertEquals(0, $result->sum('normalisasi'));
        $this->assertEquals(0, $result[0]->normalisasi);
        $this->assertEquals(0, $result[1]->normalisasi);
    }

    // ============================================================
    // 2. Utility Benefit / Cost
    // ============================================================

    #[Test]
    public function utility_benefit_tertinggi_mendapat_1(): void
    {
        $utility = $this->service->hitungUtility(nilai: 100, min: 0, max: 100, jenis: 'Benefit');
        $this->assertEquals(1.0, $utility);
    }

    #[Test]
    public function utility_benefit_terendah_mendapat_0(): void
    {
        $utility = $this->service->hitungUtility(nilai: 0, min: 0, max: 100, jenis: 'Benefit');
        $this->assertEquals(0.0, $utility);
    }

    #[Test]
    public function utility_cost_tertinggi_mendapat_0(): void
    {
        $utility = $this->service->hitungUtility(nilai: 100, min: 10, max: 100, jenis: 'Cost');
        $this->assertEquals(0.0, $utility);
    }

    #[Test]
    public function utility_cost_terendah_mendapat_1(): void
    {
        $utility = $this->service->hitungUtility(nilai: 10, min: 10, max: 100, jenis: 'Cost');
        $this->assertEquals(1.0, $utility);
    }

    #[Test]
    public function utility_min_sama_max_bukan_nol_mengembalikan_1(): void
    {
        $utility = $this->service->hitungUtility(nilai: 50, min: 50, max: 50, jenis: 'Benefit');
        $this->assertEquals(1.0, $utility);

        $utilityCost = $this->service->hitungUtility(nilai: 50, min: 50, max: 50, jenis: 'Cost');
        $this->assertEquals(1.0, $utilityCost);
    }

    #[Test]
    public function utility_min_max_nol_mengembalikan_0(): void
    {
        $utility = $this->service->hitungUtility(nilai: 0, min: 0, max: 0, jenis: 'Benefit');
        $this->assertEquals(0.0, $utility);

        $utilityCost = $this->service->hitungUtility(nilai: 0, min: 0, max: 0, jenis: 'Cost');
        $this->assertEquals(0.0, $utilityCost);
    }

    #[Test]
    public function utility_benefit_rata_rata_diantara_min_max(): void
    {
        $utility = $this->service->hitungUtility(nilai: 50, min: 0, max: 100, jenis: 'Benefit');
        $this->assertEqualsWithDelta(0.5, $utility, 0.01);
    }

    #[Test]
    public function utility_cost_rata_rata_diantara_min_max(): void
    {
        $cost = $this->service->hitungUtility(nilai: 50, min: 10, max: 90, jenis: 'Cost');
        $this->assertEqualsWithDelta(0.5, $cost, 0.01);
    }

    // ============================================================
    // 3. Perhitungan penuh (pakai data lengkap)
    // ============================================================

    /**
     * Buat data minimal untuk test integrasi: user siswa, kriteria,
     * subkriteria, alternatif, dan penilaian dummy.
     *
     * @return int  userId siswa yang sudah punya penilaian
     */
    private function buatDataPenilaian(): int
    {
        // 1. Buat user siswa
        $userId = \App\Models\User::create([
            'nama_user'    => 'Test Siswa',
            'username'     => 'testsiswa',
            'password'     => bcrypt('123456'),
            'role'         => 'Siswa',
            'nis'          => '99999',
            'is_logged_in' => false,
        ])->id_user;

        // 2. Buat 2 kriteria: Benefit + Cost
        $k1 = Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Minat', 'jenis' => 'Benefit', 'bobot' => 60]);
        $k2 = Kriteria::create(['kode_kriteria' => 'C2', 'nama_kriteria' => 'Biaya', 'jenis' => 'Cost', 'bobot' => 40]);

        // 3. Buat subkriteria dengan nilai
        $sk1High = \App\Models\Subkriteria::create(['id_kriteria' => $k1->id_kriteria, 'nama_subkriteria' => 'Tinggi', 'nilai' => 5]);
        $sk1Low  = \App\Models\Subkriteria::create(['id_kriteria' => $k1->id_kriteria, 'nama_subkriteria' => 'Rendah', 'nilai' => 2]);
        $sk2High = \App\Models\Subkriteria::create(['id_kriteria' => $k2->id_kriteria, 'nama_subkriteria' => 'Mahal', 'nilai' => 5]);
        $sk2Low  = \App\Models\Subkriteria::create(['id_kriteria' => $k2->id_kriteria, 'nama_subkriteria' => 'Murah', 'nilai' => 1]);

        // 4. Buat 2 alternatif
        $a1 = Alternatif::create(['kode_alternatif' => 'A1', 'nama_alternatif' => 'Teknik Informatika']);
        $a2 = Alternatif::create(['kode_alternatif' => 'A2', 'nama_alternatif' => 'Sistem Informasi']);

        // 5. Buat pertanyaan dummy untuk memenuhi FK
        $pertanyaan = \App\Models\Pertanyaan::create([
            'id_kriteria'      => $k1->id_kriteria,
            'id_alternatif'     => $a1->id_alternatif,
            'teks_pertanyaan'  => 'Seberapa minat kamu?',
        ]);

        // 6. Buat penilaian: A1 dapat nilai tinggi di benefit, rendah di cost (bagus)
        \Illuminate\Support\Facades\DB::table('penilaian')->insert([
            ['id_user' => $userId, 'id_alternatif' => $a1->id_alternatif, 'id_kriteria' => $k1->id_kriteria, 'id_pertanyaan' => $pertanyaan->id_pertanyaan, 'id_subkriteria' => $sk1High->id_subkriteria, 'jawaban' => 5],
            ['id_user' => $userId, 'id_alternatif' => $a1->id_alternatif, 'id_kriteria' => $k2->id_kriteria, 'id_pertanyaan' => $pertanyaan->id_pertanyaan, 'id_subkriteria' => $sk2Low->id_subkriteria, 'jawaban' => 1],
        ]);

        // A2: benefit rendah, cost rendah (jelek di minat tapi murah di biaya)
        // -> utility benefit=0, utility cost=1 → nilai_akhir = 0.4
        \Illuminate\Support\Facades\DB::table('penilaian')->insert([
            ['id_user' => $userId, 'id_alternatif' => $a2->id_alternatif, 'id_kriteria' => $k1->id_kriteria, 'id_pertanyaan' => $pertanyaan->id_pertanyaan, 'id_subkriteria' => $sk1Low->id_subkriteria, 'jawaban' => 2],
            ['id_user' => $userId, 'id_alternatif' => $a2->id_alternatif, 'id_kriteria' => $k2->id_kriteria, 'id_pertanyaan' => $pertanyaan->id_pertanyaan, 'id_subkriteria' => $sk2Low->id_subkriteria, 'jawaban' => 1],
        ]);

        return $userId;
    }

    #[Test]
    public function calculate_mengembalikan_struktur_lengkap(): void
    {
        $userId = $this->buatDataPenilaian();

        $result = $this->service->calculate(userId: $userId);

        $this->assertArrayHasKey('kriterias', $result);
        $this->assertArrayHasKey('hasil', $result);
        $this->assertInstanceOf(Collection::class, $result['kriterias']);

        foreach ($result['kriterias'] as $kriteria) {
            $this->assertIsFloat($kriteria->normalisasi);
            $this->assertGreaterThanOrEqual(0, $kriteria->normalisasi);
        }
    }

    #[Test]
    public function calculate_hasil_memiliki_field_utama(): void
    {
        $userId = $this->buatDataPenilaian();

        $result = $this->service->calculate(userId: $userId);

        $this->assertNotEmpty($result['hasil'], 'Harus ada hasil penilaian');
        $this->assertCount(2, $result['hasil'], 'Harus ada 2 alternatif yang punya penilaian');

        foreach ($result['hasil'] as $item) {
            $this->assertArrayHasKey('nama_alternatif', $item);
            $this->assertArrayHasKey('kode_alternatif', $item);
            $this->assertArrayHasKey('nilai_kriteria', $item);
            $this->assertArrayHasKey('utility', $item);
            $this->assertArrayHasKey('nilai_akhir', $item);

            $this->assertIsFloat($item['nilai_akhir']);
            $this->assertGreaterThanOrEqual(0, $item['nilai_akhir']);
            $this->assertLessThanOrEqual(1, $item['nilai_akhir'],
                'Nilai akhir harus 0-1 karena utility * normalisasi, total normalisasi = 1'
            );
        }
    }

    #[Test]
    public function calculate_urutkan_descending_bila_sort_true(): void
    {
        $userId = $this->buatDataPenilaian();

        $result = $this->service->calculate(userId: $userId, sortDescending: true);

        $this->assertIsArray($result['hasil']);
        $this->assertCount(2, $result['hasil']);

        $nilaiList = array_column($result['hasil'], 'nilai_akhir');
        $sorted = $nilaiList;
        rsort($sorted);

        $this->assertEquals($sorted, $nilaiList, 'Hasil harus terurut dari nilai tertinggi');
    }

    #[Test]
    public function calculate_tanpa_sort_mengembalikan_collection(): void
    {
        $userId = $this->buatDataPenilaian();

        $result = $this->service->calculate(userId: $userId, sortDescending: false);

        $this->assertInstanceOf(Collection::class, $result['hasil']);
        $this->assertCount(2, $result['hasil']);
    }

    // ============================================================
    // 4. Data kosong / edge case
    // ============================================================

    #[Test]
    public function calculate_tanpa_penilaian_hasil_kosong(): void
    {
        // RefreshDatabase sudah membersihkan DB antar test, jadi cukup buat data
        Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Minat', 'jenis' => 'Benefit', 'bobot' => 50]);
        Alternatif::create(['kode_alternatif' => 'A1', 'nama_alternatif' => 'Teknik Informatika']);

        $result = $this->service->calculate(userId: 999);

        $this->assertCount(0, $result['hasil']);
        $this->assertCount(1, $result['kriterias']);
    }

    #[Test]
    public function hitung_nilai_alternatif_tanpa_penilaian_return_nol(): void
    {
        // RefreshDatabase sudah membersihkan DB antar test
        $kriteria = Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Minat', 'jenis' => 'Benefit', 'bobot' => 100]);
        $alternatif = Alternatif::create(['kode_alternatif' => 'A1', 'nama_alternatif' => 'Teknik Informatika']);

        $kriterias = $this->service->normalizeBobot(collect([$kriteria]));
        $minMax = $this->service->hitungMinMaxPerKriteria($kriterias, userId: 999);

        $item = $this->service->hitungNilaiAlternatif($alternatif, 999, $kriterias, $minMax);

        $this->assertEquals('Teknik Informatika', $item['nama_alternatif']);
        $this->assertEquals(0.0, $item['nilai_akhir'], 'Tanpa penilaian, nilai akhir harus 0');
    }
}
