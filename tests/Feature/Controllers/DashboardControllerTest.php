<?php

namespace Tests\Feature\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Pertanyaan;
use App\Models\Subkriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk DashboardController.
 *
 * Memastikan logic status siswa (belum/sedang/selesai) dan statistik
 * agregat di dashboard berjalan benar.
 */
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        // Login sebagai Guru BK
        $this->guru = User::create([
            'nama_user' => 'Pak Guru',
            'username'  => 'guru_test',
            'password'  => bcrypt('123456'),
            'role'      => 'Guru BK',
        ]);
    }

    /**
     * Setup data: 1 kriteria, 2 subkriteria, 1 alternatif, 1 pertanyaan.
     */
    private function setupMinimumData(): array
    {
        $kriteria = Kriteria::create([
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Minat',
            'jenis'         => 'Benefit',
            'bobot'         => 100,
        ]);

        $subkriteria = Subkriteria::create([
            'id_kriteria'     => $kriteria->id_kriteria,
            'nama_subkriteria' => 'Tinggi',
            'nilai'           => 5,
        ]);

        $alternatif = Alternatif::create([
            'kode_alternatif'  => 'A1',
            'nama_alternatif'  => 'Teknik Informatika',
        ]);

        $pertanyaan = Pertanyaan::create([
            'id_kriteria'      => $kriteria->id_kriteria,
            'id_alternatif'    => $alternatif->id_alternatif,
            'teks_pertanyaan'  => 'Test pertanyaan',
        ]);

        return [
            'kriteria'   => $kriteria,
            'subkriteria' => $subkriteria,
            'alternatif' => $alternatif,
            'pertanyaan' => $pertanyaan,
        ];
    }

    private function createSiswa(string $nama, string $username, int $nis): User
    {
        return User::create([
            'nama_user' => $nama,
            'username'  => $username,
            'password'  => bcrypt('123456'),
            'role'      => 'Siswa',
            'nis'       => (string) $nis,
        ]);
    }

    // ============================================================
    // 1. Statistik cards
    // ============================================================

    #[Test]
    public function dashboard_menampilkan_total_semua_entitas(): void
    {
        $this->setupMinimumData();
        $this->createSiswa('Andi', 'andi', 12345);
        $this->createSiswa('Budi', 'budi', 12346);

        $response = $this->actingAs($this->guru)->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHas('total_kriteria', 1);
        $response->assertViewHas('total_alternatif', 1);
        $response->assertViewHas('total_subkriteria', 1);
        $response->assertViewHas('total_users', 3); // 1 guru + 2 siswa
        $response->assertViewHas('total_siswa', 2);
    }

    #[Test]
    public function dashboard_dengan_data_kosih_masih_render_normal(): void
    {
        $response = $this->actingAs($this->guru)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('total_kriteria', 0);
        $response->assertViewHas('total_siswa', 0);
    }

    // ============================================================
    // 2. Status siswa: belum / sedang / selesai
    // ============================================================

    #[Test]
    public function siswa_tanpa_penilaian_status_belum(): void
    {
        $this->setupMinimumData();
        $siswa = $this->createSiswa('Andi', 'andi', 12345);

        $response = $this->actingAs($this->guru)->get('/');
        $siswaStatus = $response->viewData('siswa_status');

        $this->assertCount(1, $siswaStatus);
        $this->assertEquals('belum', $siswaStatus->first()->status);
        $this->assertEquals('Belum Mengisi', $siswaStatus->first()->status_label);
        $this->assertEquals('bg-danger', $siswaStatus->first()->status_class);
    }

    #[Test]
    public function siswa_dengan_sebagian_penilaian_status_sedang(): void
    {
        $data = $this->setupMinimumData();

        // Tambah pertanyaan kedua agar bisa "sebagian"
        Pertanyaan::create([
            'id_kriteria'      => $data['kriteria']->id_kriteria,
            'id_alternatif'    => $data['alternatif']->id_alternatif,
            'teks_pertanyaan'  => 'Pertanyaan kedua',
        ]);

        $siswa = $this->createSiswa('Andi', 'andi', 12345);

        // Jawab hanya 1 dari 2 pertanyaan
        Penilaian::create([
            'id_user'        => $siswa->id_user,
            'id_alternatif'  => $data['alternatif']->id_alternatif,
            'id_kriteria'    => $data['kriteria']->id_kriteria,
            'id_pertanyaan'  => $data['pertanyaan']->id_pertanyaan,
            'id_subkriteria' => $data['subkriteria']->id_subkriteria,
            'jawaban'        => 5,
        ]);

        $response = $this->actingAs($this->guru)->get('/');
        $siswaStatus = $response->viewData('siswa_status');

        $this->assertEquals('sedang', $siswaStatus->first()->status);
        $this->assertEquals('Sedang Mengisi', $siswaStatus->first()->status_label);
        $this->assertEquals(50, $siswaStatus->first()->progress);
    }

    #[Test]
    public function siswa_dengan_semua_penilaian_status_selesai(): void
    {
        $data = $this->setupMinimumData();
        $siswa = $this->createSiswa('Andi', 'andi', 12345);

        // Jawab 1 pertanyaan (total pertanyaan = 1)
        Penilaian::create([
            'id_user'        => $siswa->id_user,
            'id_alternatif'  => $data['alternatif']->id_alternatif,
            'id_kriteria'    => $data['kriteria']->id_kriteria,
            'id_pertanyaan'  => $data['pertanyaan']->id_pertanyaan,
            'id_subkriteria' => $data['subkriteria']->id_subkriteria,
            'jawaban'        => 5,
        ]);

        $response = $this->actingAs($this->guru)->get('/');
        $siswaStatus = $response->viewData('siswa_status');

        $this->assertEquals('selesai', $siswaStatus->first()->status);
        $this->assertEquals('Selesai', $siswaStatus->first()->status_label);
        $this->assertEquals(100, $siswaStatus->first()->progress);
    }

    #[Test]
    public function summary_count_belum_sedang_selesai_sesuai_data(): void
    {
        $data = $this->setupMinimumData();
        Pertanyaan::create([
            'id_kriteria'      => $data['kriteria']->id_kriteria,
            'id_alternatif'    => $data['alternatif']->id_alternatif,
            'teks_pertanyaan'  => 'P2',
        ]);
        $totalPertanyaan = 2;

        // Siswa 1: belum (0 jawaban)
        $this->createSiswa('Belum', 'belum', 1);

        // Siswa 2: sedang (1 dari 2 jawaban)
        $sedang = $this->createSiswa('Sedang', 'sedang', 2);
        Penilaian::create([
            'id_user'        => $sedang->id_user,
            'id_alternatif'  => $data['alternatif']->id_alternatif,
            'id_kriteria'    => $data['kriteria']->id_kriteria,
            'id_pertanyaan'  => $data['pertanyaan']->id_pertanyaan,
            'id_subkriteria' => $data['subkriteria']->id_subkriteria,
            'jawaban'        => 5,
        ]);
        $this->assertEquals(1, Penilaian::where('id_user', $sedang->id_user)->count());

        // Siswa 3: selesai (2 dari 2 jawaban, semua pertanyaan)
        $selesai = $this->createSiswa('Selesai', 'selesai', 3);
        foreach (Pertanyaan::all() as $p) {
            Penilaian::create([
                'id_user'        => $selesai->id_user,
                'id_alternatif'  => $data['alternatif']->id_alternatif,
                'id_kriteria'    => $data['kriteria']->id_kriteria,
                'id_pertanyaan'  => $p->id_pertanyaan,
                'id_subkriteria' => $data['subkriteria']->id_subkriteria,
                'jawaban'        => 5,
            ]);
        }
        $this->assertEquals($totalPertanyaan, Penilaian::where('id_user', $selesai->id_user)->count());

        $response = $this->actingAs($this->guru)->get('/');

        $response->assertViewHas('belum_isi', 1);
        $response->assertViewHas('sedang_isi', 1);
        $response->assertViewHas('selesai_isi', 1);
    }

    #[Test]
    public function guru_tidak_muncul_di_status_siswa(): void
    {
        // 1 guru (sudah ada di setUp) + 1 siswa
        $this->setupMinimumData();
        $this->createSiswa('Andi', 'andi', 1);

        $response = $this->actingAs($this->guru)->get('/');
        $siswaStatus = $response->viewData('siswa_status');

        // Hanya siswa yang muncul, guru tidak
        $this->assertCount(1, $siswaStatus);
        $this->assertEquals('Siswa', $siswaStatus->first()->role ?? 'Siswa');
    }

    // ============================================================
    // 3. Statistik penilaian
    // ============================================================

    #[Test]
    public function total_penilaian_menghitung_siswa_unik_yang_sudah_nilai(): void
    {
        $data = $this->setupMinimumData();
        $siswa1 = $this->createSiswa('Siswa 1', 's1', 1);
        $siswa2 = $this->createSiswa('Siswa 2', 's2', 2);

        // Siswa 1: 2 penilaian (untuk alternatif sama, kriteria beda)
        Penilaian::create([
            'id_user'        => $siswa1->id_user,
            'id_alternatif'  => $data['alternatif']->id_alternatif,
            'id_kriteria'    => $data['kriteria']->id_kriteria,
            'id_pertanyaan'  => $data['pertanyaan']->id_pertanyaan,
            'id_subkriteria' => $data['subkriteria']->id_subkriteria,
            'jawaban'        => 5,
        ]);

        // Siswa 2: 1 penilaian
        Penilaian::create([
            'id_user'        => $siswa2->id_user,
            'id_alternatif'  => $data['alternatif']->id_alternatif,
            'id_kriteria'    => $data['kriteria']->id_kriteria,
            'id_pertanyaan'  => $data['pertanyaan']->id_pertanyaan,
            'id_subkriteria' => $data['subkriteria']->id_subkriteria,
            'jawaban'        => 5,
        ]);

        $response = $this->actingAs($this->guru)->get('/');
        // 2 siswa unik, bukan 3 baris penilaian
        $response->assertViewHas('total_penilaian', 2);
    }

    // ============================================================
    // 4. Auth required
    // ============================================================

    #[Test]
    public function dashboard_memerlukan_authentication(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }
}
